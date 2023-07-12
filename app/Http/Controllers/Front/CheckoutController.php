<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Service\Order\OrderServiceInterface;
use App\Service\OrderDetail\OrderDetailServiceInterface;
use App\Utilities\Constant;
use App\Utilities\VNPay;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\AddOderRequest;

class CheckoutController extends Controller
{
    private $orderService;
    private $orderDetailService;

    public function __construct(OrderServiceInterface $orderService,
                                OrderDetailServiceInterface $orderDetailService)
    {
        $this->orderService = $orderService;
        $this->orderDetailService = $orderDetailService;
    }

    public function checkout()
    {
        $carts = Cart::content();
        $total = Cart::total();
        $subtotal = Cart::subtotal();

        return view('Front.Checkout.checkout', compact('carts', 'total', 'subtotal'));
    }

    public function addOrder(AddOderRequest $request)
    {
        //01 Thêm đơn hàng
        $data = $request->all();
        $data['status'] = Constant::order_status_ReceiveOrders;
        $order = $this->orderService->create($data);

        //o2: Thêm chi tiết đơn hàng

        $carts = Cart::content();

        foreach ($carts as $cart) {
            $data = [
                'order_id' => $order->id,
                'product_id' => $cart->id,
                'qty' => $cart->qty,
                'amount' => $cart->price,
                'total' => $cart->qty * $cart->price,
            ];
            $this->orderDetailService->create($data);
        }

        if ($request->payment_type == 'pay_later') {
            //Gửi Email
            $total = Cart::total();
            $subtotal = Cart::subtotal();
            $this->sendEmail($order, $total, $subtotal); //Gọi hàm gửi Email qua định nghĩa

            //03 Xóa giỏ hàng

            Cart::destroy();

            //04 Trả về kết quả thông báo

            return redirect('checkout/result')
                ->with('notification', 'Success! You will pay on delivery. Please check your email.');
        }
        if ($request->payment_type == 'online_payment') {
            //01. Lấy URL thanh toán VNPay
            $data_url = VNPay::vnpay_create_payment([
                'vnp_TxnRef' => $order->id, //ID đơn hàng.
                'vnp_OrderInfo' => 'Mô tả đơn hàng ở đây...', //Mô tả đơn hàng(điền tùy ý phù hợp)
                'vnp_Amount' => Cart::total(0, '', '') * 23.447, //Tổng giá của đơn hàng
            ]);

            //02. Chuyển hướng tới URL lấy được:
            return redirect()->to($data_url);
        }

    }

    public function vnPayCheck(AddOderRequest $request)
    {
        //01. Lấy data từ URL (do VNPay gửi về qua $vnp_Returnurl)
        $vnp_ResponseCode = $request->get('vnp_ResponseCode'); //Mã phản hồi kết quả thanh toán
        $vnp_TxnRef = $request->get('vnp_TxnRef'); //order_id
        $vnp_Amount = $request->get('vnp_Amount'); //Số tiền thanh toán

        //02. Kiểm tra data, xem kết quả giao dịch trả về từ VNPay hợp ệ không
        if($vnp_ResponseCode != null)
        {
            //Nếu thành công:
            if($vnp_ResponseCode == 00) {
                //Cập nhật trạng thái Order
                $this->orderService->update(['status' => Constant::order_status_Paid], $vnp_TxnRef);
                //Gửi Email:
                $order = $this->orderService->find($vnp_TxnRef);
                $total = Cart::total();
                $subtotal = Cart::subtotal();
                $this->sendEmail($order, $total, $subtotal); //Gọi hàm gửi Email qua định nghĩa

                //Xóa giỏ hàng
                Cart::destroy();

                //Thông báo kết quả
                return redirect('checkout/result')
                    ->with('notification', 'Success! Has paid online. Please check your email.');
            } else {
                //Xóa đơn hàng đã thêm vào database
                $this->orderService->delete($vnp_TxnRef);

                //Thông báo lỗi
                return redirect('checkout/result')
                    ->with('notification', 'ERROR: Payment failed or canceled.');
            }
        }
    }

    public function result()
    {
        $notification = session('notification');
        return view('Front.Checkout.result', compact('notification'));
    }

    private function sendEmail($order, $total, $subtotal)
    {
        $email_to = $order->email;

        Mail::send('Front.Checkout.email', compact('order', 'total', 'subtotal'), function ($message) use ($email_to){
            $message->from('hoathioanh07082000@gmail.com', 'webthoitrang');
            $message->to($email_to, $email_to);
            $message->subject('Order Notification');
        });
    }
}
