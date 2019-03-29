<?php
/**
 * Created by PhpStorm.
 * User: yss
 * Date: 18/2/9
 * Time: 上午10:34
 */
namespace App\Libs;

class ReturnCode
{
    //通用(8001-8109)
    const SUCCESS            = 0; //成功
    const FAILED             =2; //失败
    const FORBIDDEN          = 8001; //权限不足
    const SYSTEM_FAIL        = 8002; //系统错误，如数据写入失败之类的
    const PARAMS_ERROR       = 8003; //参数错误
    const NOT_FOUND          = 8004; //资源未找到
    const ACCESS_TOKEN_ERROR = 8005; //access_token错误
    const AUTHORIZE_FAIL     = 8006; //权限验证失败
    const NOT_MODIFY         = 8007; //没有变动
    const RECORD_EXIST       = 8008; //记录已存在
    const SIGN_FAIL          = 8009; //签名错误
    const RECORD_NOT_EXIST   = 8010; //记录不存在

    //参数相关
    const EMAIL_EXIST           = 8201; //邮箱已存在
    const EMAIL_FORMAT_FAIL     = 8202; //邮箱格式不对正确
    const MOBILE_FORMAT_FAIL    = 8203; //手机格式不正确
    const MOBILE_NOT_FIND       = 8204; //手机号码不存在
    const MOBILE_HAS_MORE       = 8205; //存在多个手机号码
    const NAME_EXIST            = 8206; //名称已被使用
    const NAME_REQUIRED         = 8209; //名称已被使用
    const MOBILE_EXIST          = 8207; //手机号已存在
    const HAVE_NO_QUOTA         = 8208; // 名额已满
    const SHOP_NOT_EXIST        = 8210; // 商铺不存在
    const GOODS_NOT_EXIST       = 8211; // 商品不存在
    const GOODS_SKU_NOT_EXIST   = 8212; // SKU不存在
    const USER_NOT_EXIST        = 8213; // 用户不存在
    const INVALID_GOODS_NUM     = 8214; // 购买商品数量非法
    const INVALID_IDCARD        = 8215; // 身份证号码非法
    const INVALID_ORDER_PRICE   = 8216; // 订单金额非法
    const PUBLICID_NOT_EXIST    = 8217; // publicId不存在
    const AMOUNT_NOT_ENOUGH     = 8218; // 提现余额不足
    const WITHDRAW_NOT_EXIST    = 8219; // 提现账户未设置或设置有误
    const INVALID_VERIFY_CODE   = 8220; // 验证码错误
    const VERIFY_CODE_SEND_FAIL = 8221; // 验证码发送失败
    const SMS_SEND_FAIL         = 8222; //短信发送失败
    const CART_IS_EMPTY         = 8225; // 购物车为空

    //商品相关
    const SN_EXIST                   = 8301; //商品编码已被使用
    const SHIPPING_TEMPLATE_REQUIRED = 8302; //运费模板必填
    const DELETE_GOODS_ATTR_FAIL     = 8303; //删除商品属性失败
    const GOODS_REBATE_WRONG         = 8304; //商品收益必须大于0.1元
    const GOODS_NOT_DRAFT            = 8310; //商品非草稿状态
    const GOODS_SUBSCRIBED           = 8311; // 商品已选择
    const GOODS_UNSUBSCRIBED         = 8312; // 商品未选择
    const GOODS_STOCK_NOT_ENOUGH     = 8313; // 商品库存不足
    const SKU_STOCK_NOT_ENOUGH       = 8314; // SKU库存不足
    const GOODS_SN_REQUIRED          = 8315; //商品编码不能为空
    const GOODS_WEIGHT_REQUIRED      = 8316; //商品重量必填
    const BAR_CODE_EXIST             = 8317; //商品条形码已被使用

    //登录、账号相关
    const USERNAME_REQUIRED      = 8401; //登录账号为必填
    const PASSWORD_REQUIRED      = 8402; //登录密码为必填
    const USERNAME_EXIST         = 8403; //登录账号已被使用
    const ADMINNAME_REQUIRED     = 8404; //管理员姓名不能为空
    const PASSWORD_NOT_MATCH     = 8405; //密码错误
    const OLD_PASSWORD_NOT_MATCH = 8406; //旧密码不匹配
    const PASSWORD_CONFIRM_FAIL  = 8407; //两次输入的密码不匹配
    const PASSWORD_FORMAT_FAIL   = 8408; //密码格式不对
    const APPLY_SIGN_FAIL        = 8510; //注册邀请码错误

    //会议
    const GUEST_WAS_CHECKED = 9001;
    const MEET_IS_ENDED     = 9002;

    //订单相关
    const STATUS_NOT_EDIT     = 8801; //当前状态不能操作
    const SHIPPING_FEE_ERROR  = 8802; //邮费不能小于0或大于10000
    const ORDER_AMOUNT_ERROR  = 8803; //订单金额金额有误
    const SHIPPING_NO_ERROR   = 8804; //运单号格式有误
    const ORDER_PAID          = 8810; //订单已支付
    const ORDER_PAY_NOT_EXIST = 8811; // 订单对应的支付单号不存在
    const ORDER_CANCEL_REFUSE = 8812; // 拒绝取消订单
    const ORDER_NOT_PAID      = 8813; // 订单未支付
    const ORDER_CANCELED      = 8814; // 订单已取消
    const ADRESS_ERROR        = 8815;   //地址有误或无法配送
    const STOCK_NOT_ENOUGH   = 8816;   //库存不足


    const CATEGORY_USED_CANNOT_DEL    = 8501; //类目被使用，不能删除
    const CATEGORY_HAS_SUB_CANNOT_DEL = 8502; //类目存在下级，不能删除
    //仓库
    const CANNOT_DELETE_USED = 8910; //使用中

    //中文错误详情
    public static $codeTexts = [
        0    => '操作成功',
        8001 => '权限不足',
        8002 => '系统错误，请联系管理员',
        8003 => '参数错误',
        8004 => '资源未找到',
        8005 => 'TOKEN无效',
        8006 => '权限不足',
        8007 => '没有修改',
        8008 => '记录已存在',
        8009 => '签名错误',
        8010 => '记录不存在',
        //参数错误
        8201 => '邮箱已存在',
        8202 => '邮箱格式不对正确',
        8203 => '手机格式不正确',
        8204 => '手机号码不存在',
        8205 => '存在多个手机号码',
        8206 => '名称已被使用',
        8207 => '手机号已存在',
        8208 => '名额已满',
        8209 => '名称为必填项',
        8210 => '商铺不存在',
        8211 => '商品不存在',
        8212 => 'SKU不存在',
        8213 => '用户不存在',
        8214 => '购买商品数量有误',
        8215 => '身份证号码有误',
        8216 => '订单金额有误',
        8217 => 'publicId未知',
        8218 => '余额不足',
        8219 => '提现账户未设置或设置有误',
        8220 => '验证码不正确',
        8221 => '验证码发送失败，请核对您的国家区号和手机号是否正确',
        8222 => '短信发送失败',
        8225 => '购物车为空',
        //商品相关
        8301 => '商品编码已被使用',
        8302 => '运费模板为必填项',
        8303 => '删除商品属性失败',
        8304 => '商品收益必须大于0.1元',
        8310 => '商品非草稿状态，不能执行此操作',
        8311 => '商品已手选',
        8312 => '商品未手选',
        8313 => '商品库存不足',
        8314 => '库存不足',
        8315 => '商品编码不能为空',
        8316 => '商品重量必填',
        8317 => '商品条形码已被使用',
        //登录、账号相关
        8401 => '登录账号为必填',
        8402 => '登录密码为必填',
        8403 => '登录账号已被使用',
        8404 => '管理员姓名不能为空',
        8405 => '登录失败',
        8406 => '原密码不匹配',
        8407 => '两次输入的密码不匹配',
        8408 => '密码格式错误，请输入%s到%s位字符',
        8510 => '注册邀请码不存在或已被使用',
        //订单相关
        8801 => '当前状态不能操作',
        8802 => '邮费不能小于0或大于10000',
        8803 => '订单金额金额有误',
        8804 => '运单号格式有误',
        8810 => '订单已支付',
        8811 => '订单对应的支付单号不存在',
        8812 => '拒绝取消订单',
        8813 => '订单未支付',
        8814 => '订单已取消',
        8815 => '地址有误或当前地区无法配送',
        8816 => '库存不足',

        //类型相关
        8501 => '类目已被使用，不能删除',
        8502 => '类目存在下级，不能删除',
        //仓库
        8910 => '已被使用，不能删除',
        //会议
        9001 => '您已经签到过了',
        9002 => '报名已结束',
    ];

    public static function create($code, $data = [], $msg = '')
    {
        if (empty($msg) && isset(self::$codeTexts[$code])) {
            $msg = self::$codeTexts[$code];
        }

        return ['code' => $code, 'msg' => $msg, 'data' => $data];
    }

    public static function success($data = [], $msg = '')
    {
        if (empty($msg) && isset(self::$codeTexts[self::SUCCESS])) {
            $msg = self::$codeTexts[self::SUCCESS];
        }
        return ['code' => self::SUCCESS, 'msg' => $msg, 'data' => $data];
    }

    public static function error($code, $msg = '')
    {
        if (empty($msg) && isset(self::$codeTexts[$code])) {
            $msg = self::$codeTexts[$code];
        }
        return ['code' => $code, 'msg' => $msg];
    }
}
