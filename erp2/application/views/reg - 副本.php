<?php $this->load->view('header');?>
<script type="text/javascript">
    var DOMAIN = document.domain;
    var WDURL = "";
    var SCHEME= "<?php echo sys_skin()?>";
    try{
        document.domain = '<?php echo base_url()?>';
    }catch(e){
    }
    //ctrl+F5 增加版本号来清空iframe的缓存的
    $(document).keydown(function(event) {
        /* Act on the event */
        if(event.keyCode === 116 && event.ctrlKey){
            var defaultPage = Public.getDefaultPage();
            var href = defaultPage.location.href.split('?')[0] + '?';
            var params = Public.urlParam();
            params['version'] = Date.parse((new Date()));
            for(i in params){
                if(i && typeof i != 'function'){
                    href += i + '=' + params[i] + '&';
                }
            }
            defaultPage.location.href = href;
            event.preventDefault();
        }
    });
</script>
<!DOCTYPE html>
<html>
<head>
    <title>进销存</title>
    <meta name="globalsign-domain-verification" content="wnLJy1jTEsQbKd3ZepUI9lK4R1lnQif9O4mKSlu1rX"/>
    <meta name="viewport" content='width=device-width,initial-scale=0.4; maximum-scale=3.0;minimum-scale:0.5;user-scalable=yes;'/>
    <link href="<?php echo base_url() ?>statics/login/Css/common.css" rel="stylesheet"/>
    <link href="<?php echo base_url() ?>statics/login/Css/reg.css" rel="stylesheet"/>
    <link href="<?php echo base_url() ?>statics/login/Css/global.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="<?php echo base_url() ?>statics/login/Images/bitbug_favicon.ico"
          type="image/x-icon"/>
    <link rel="apple-touch-icon" href="<?php echo base_url() ?>statics/login/Images/WebIcon/apple-touch-icon-57.png"/>
    <link rel="apple-touch-icon" sizes="72x72"
          href="<?php echo base_url() ?>statics/login/Images/WebIcon/apple-touch-icon-72.png"/>
    <link rel="apple-touch-icon" sizes="114x114"
          href="<?php echo base_url() ?>statics/login/Images/WebIcon/apple-touch-icon-114.png"/>
    <link rel="apple-touch-icon" sizes="144x144"
          href="<?php echo base_url() ?>statics/login/Images/WebIcon/apple-touch-icon-144.png"/>
    <script src="<?php echo base_url() ?>statics/login/Scripts/minijs/jquery-1.7.1.js"></script>
    <script src="<?php echo base_url() ?>statics/login/Scripts/minijs/common.js"></script>
    <script src="<?php echo base_url() ?>statics/login/Scripts/minijs/minicheck.js"></script>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        #register-box {
            visibility: visible;
            box-shadow: none;
            top: 20px;
            font-size: 12px;
        }

        .span_label {
            font-size: 12px;
        }

        #userIdentity_div input {
            height: 16px;
            line-height: 16px;
            width: 16px;
        }

        .reveal-modal-con a.reg_validatacode_a,
        .reveal-modal-con a.reg_validatacode_b {
            right: 19px !important;
            top: 2px !important;
        }

        .reveal-modal-con dd input#reg_btn_r,
        .reveal-modal-con dd input#reg_btn2_r {
            width: 100%;
            color: #fff;
            font-size: 19px;
            display: block;
            margin: auto;
            height: 43px;
            line-height: 43px;
            border-radius: 30px;
            background: #3cbbfe;
            background: -webkit-linear-gradient(to right, #36d3ea, #3cbbfe);
            background: linear-gradient(to right, #36d3ea, #3cbbfe);
        }

        .reveal-modal-con dd input#reg_btn_r:hover,
        .reveal-modal-con dd input#reg_btn2_r:hover {
            background: #3cbbfe;
            background: -webkit-linear-gradient(to left, #36d3ea, #3cbbfe);
            background: linear-gradient(to left, #36d3ea, #3cbbfe);
            cursor: pointer;
        }

        .formErrorContent {
            width: auto !important;
            min-width: 161px !important;
            max-width: 226px !important;
        }
        .msgs{
            font-size: 5px;;
        }
        #registerBtn{
            width: 100%;
            color: #fff;
            font-size: 19px;
            display: block;
            margin: auto;
            height: 43px;
            line-height: 43px;
            border-radius: 30px;
            background: #3cbbfe;
            background: -webkit-linear-gradient(to right, #36d3ea, #3cbbfe);
            background: linear-gradient(to right,#36d3ea, #3cbbfe);
        }
        .valid-msg{
            color: #dd0000;
        }

        .register-form .valid-msg i {
            position: absolute;
            left: 0;
            top: 10px;
            width: 16px;
            height: 16px;
            display: block;
            background: url(/statics/css/img/spr_icons.png) no-repeat;

        }
        .register-form .valid-error i {
            background-position: -16px 0;
        }
        .valid-msg span {
            padding-left: 5px;
            font-size: 10px;
        }

        .register-form .valid-error {
            color: #dd4e4e;
            line-height: 1.5;
        }
        .register-form .valid-msg, .register-form .loadings {
            float: right;
            display: inline;
            height: 20px;
            vertical-align: middle;
            position: relative;
            padding-left: 12px;
            line-height: 2.5;
        }

        .register-form .loadings i {
            position: absolute;
            left: -5px;
            top: 9px;
        }

        .ui-icon-loading {


            width: 16px;
            height: 16px;
            display: block;
            background: url(/statics/css/img/indicator.gif) no-repeat;
        }
        .valid-success{
            left: -123px;
        }
    </style>
</head>
<body class="regPage" id="body">
<div class="LoginBox">
    <div class="LoginLog"></div>

</div>
    <div class="margin_div_center" >
        <div id="register-box" class="reveal-modal reveal-modal-page-reg" style="">
            <div class="reveal-modal-con" style="padding:18px 0 18px 0;">
                <dl>
                    <dt><strong>注册</strong></dt>
                    <br>
                    <dd style="margin-top:0">
                        <div id="registerTip" style="color:red;"></div>
                    </dd>
                    <form action="#" id="registerForm" class="register-form">
                        <dd >
                            <span class="span_label">用户名</span>
                            <input type="text" class="ui-input" id="userName" name="userName"/>
                            <div class="ctn-wrap">
                                <p class="msgs" >用户名由4-20个英文字母或数字组成（不支持中文，不区分大小写字母）。一旦创建成功，不可修改。</p>
                            </div>
                        </dd>
                        <dd >
                            <span class="span_label">登录密码</span>

                            <input type="password" placeholder="8~20位数字和字母组合" class="ui-input" id="password" name="password" style="ime-mode
:disabled;" onpaste="return false;"/>
                                <div class="pswStrength" id="pswStrength" style="display:none;">
                                    <p>密码强度</p>
                                    <b></b>
                                    <b></b>
                                    <b></b>
                                </div>
                                <p class="msgs">密码由6-20个英文字母（区分大小写）或数字或特殊符号组成。</p>

                        </dd>
                        <dd >
                            <span class="span_label">确认密码</span>

                                <input type="password" class="ui-input" id="pswConfirm" name="pswConfirm" style="ime-mode:disabled;" onpaste="return false;"/>

                        </dd>




                        <dd >
                            <span class="span_label">真实姓名</span>
                                <input type="text" class="ui-input" id="realName" name="realName"/>
                                <p class="msgs">真实姓名将应用在单据和账表打印中，请如实填写</p>

                        </dd>
                        <dd >
                            <span class="span_label">常用手机</span>
                                <input type="text" placeholder="请输入手机号码" class="ui-input" id="userMobile" name="userMobile"/>
                                <p class="msgs">手机将作为找回密码的重要依据</p>

                        </dd>




<!--                        <dd class="reg_validatacode_dd">-->
<!--                            <span class="span_label">短信验证码</span>-->
<!--                            <input type="text" name="reg_validatacode" id="reg_validatacode" placeholder="短信验证码"-->
<!--                                   maxlength="6" class="validate[required,funcCall[checkPhone]]"><a-->
<!--                                class="reg_validatacode_a" href="javascript:">获取验证码</a>-->
<!--                            <a class="reg_validatacode_b" href="javascript:">60s后再获取...</a>-->
<!--                        </dd>-->

<!--                        <dd class="center">-->
<!--                            <input id="reg_agreement" type="checkbox" style="width:16px; height:16px;" checked=""> 同意-->
<!--                            <a href="/agreement/" target="_blank" style="color: #00b9ef;">“精斗云用户协议”</a>和<a-->
<!--                                href="/conceal/" target="_blank" style="color: #00b9ef;">“精斗云客户隐私”</a>-->
<!--                        </dd>-->

                        <dd>

                            <input type="button" value="免费开通" id="registerBtn">

                        </dd>

                        <dd style="text-align:center">
                            <div class="login_btn_div">
                                <a href="<?php echo site_url('login')?>" id="has_yzj" >已有帐号，请登录</a>
                            </div>
                        </dd>
                    </form>


                </dl>

            </div>
        </div>
    </div>






<!--需要loading 的页面就在页面最下方加-->

<script src="<?php echo base_url()?>/statics/js/dist/register.js?ver=20140430"></script>
</body>
</html>
