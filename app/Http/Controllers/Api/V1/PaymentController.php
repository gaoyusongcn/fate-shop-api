<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Events\OrderPaid;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * 微信小程序支付
     *
     * @param Request $request
     * @param $id
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function payByWechatMiniapp(Request $request, $id) {
        $order = Order::query()->findOrFail($id);
        $this->authorize('own', $order);

        // 校验订单状态
        if ($order->paid_at || $order->closed) {
            return $this->response->errorForbidden('订单状态不正确');
        }

        return app('wechat_pay')->miniapp([
            'out_trade_no' => $order->no,  // 商户订单流水号，与支付宝 out_trade_no 一样
            'total_fee'    => $order->total_amount * 100, // 与支付宝不同，微信支付的金额单位是分。
            'body'         => '支付的订单：' . $order->no, // 订单描述
            'openid'       => $request->user()->openid,
        ]);
    }

    /**
     * 微信支付服务器端回调
     *
     * @return string
     */
    public function wechatNotify()
    {
        // 校验回调参数是否正确
        $data  = app('wechat_pay')->verify();

        $order = Order::where('no', $data->out_trade_no)->first();

        if (! $order) {
            return 'fail';
        }

        if ($order->paid_at) {
            // 告知微信支付此订单已处理
            return app('wechat_pay')->success();
        }

        // 将订单标记为已支付
        $order->update([
            'paid_at'        => Carbon::now(),
            'payment_method' => 'wechat',
            'payment_no'     => $data->transaction_id,
        ]);

        $this->afterPaid($order);

        return app('wechat_pay')->success();
    }

    /**
     * 支付完成后触发事件
     *
     * @param Order $order
     */
    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }
}
