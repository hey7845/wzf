<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>操作提示</title>
	</head>
	<style>
*{margin: 0 auto;padding: 0;}
.tranjump{background: url(/public/images/tac.jpg) no-repeat;width: 100%;height: 100%;}
.rectangle_box{position: absolute;left: 50%;right: 50%;top: 50%;bottom: 50%;margin-left: -189.5px;margin-top: -77px;}
.borderopely{width: 363px;height: 138px;border: 8px solid rgba(255,255,255,0.6);}
.tipbox{width: 363px;height: 138px;background: white;text-align: center;}
.welcople{color: #666666;font-size: 16px;padding-top: 18px;}
.welcople .litps{background: url(/public/images/socp.png) no-repeat;width: 31px;height: 31px;display: inline-block;vertical-align: text-bottom;}
.welcople .modifysu{background: url(/public/images/socp2.png) no-repeat;width: 34px;height: 37px;}
.welcople .modifyfail{background: url(/public/images/socp3.png) no-repeat;width: 24px;height: 28px;}
.ma-to-30{margin-top: 30px;}
.ma-to-20{margin-top: 20px;}
.ma-to-10{margin-top: 10px;}
.cloblu{color: #3c8dbc;font-size: 16px;}
.waittim{color: #666666;font-size: 16px;margin-left: 10px;}
.ht-colblue{color: #3c8dbc;}
a{text-decoration: none;}
.waittim em{color: #3c8dbc;}
em{font-style: normal;}
.recigle{width: 379px;text-align: center;margin-top: 10px;}
.recigle p{color: #333333;font-size: 16px;line-height: 20px;}
.recigle .copyright{color: #3c8dbc;}
	</style>
	<body class="tranjump">
		<div class="rectangle_box">
			<div class="borderopely">
								<!--修改失败-s-->
				<div class="tipbox" style="">
					<div class="welcople">
						<i class="litps modifyfail"></i>请先登录					</div>
					<div class="ma-to-20">
						<span class="cloblu">页面自动跳转中...</span>
						<span class="waittim"><a id="href" href="/index.php/Admin/Admin/login" style="color: #666666">等待时间：<em id="wait">3</em></a></span>
					</div>
					<div class="ma-to-10">
						<a href="/"  target="_parent" class="cloblu">网站前台</a>
						<a href="/index.php?m=Admin&c=Index&a=index" target="_parent" class="waittim ht-colblue">管理员后台</a>
					</div>
				</div>
				<!--修改失败-e-->
				 
			</div>
			<div class="recigle">
				<p>Copyright©2014-2017<em class="copyright"><!-- <a class="copyright" href="http://www.tpshop.cn/">TPSHOP v2.0.0</a> --></em></p>
				<p><em class="copyright"><a class="copyright" href="http://www.tpshop.cn/">会员管理系统</a></em>出品</p>
			</div>
		</div>
		<script type="text/javascript">
		(function(){
			var wait = document.getElementById('wait'),href = document.getElementById('href').href;
			var interval = setInterval(function(){
				var time = --wait.innerHTML;
				if(time <= 0) {
					location.href = href;
					clearInterval(interval);
				};
			}, 1000);
			})();
		</script>   
	<div id="think_page_trace" style="position: fixed;bottom:0;right:0;font-size:14px;width:100%;z-index: 999999;color: #000;text-align:left;font-family:'微软雅黑';">
    <div id="think_page_trace_tab" style="display: none;background:white;margin:0;height: 250px;">
        <div id="think_page_trace_tab_tit" style="height:30px;padding: 6px 12px 0;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px">
                        <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">基本</span>
                        <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">文件</span>
                        <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">流程</span>
                        <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">错误</span>
                        <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">SQL</span>
                        <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700">调试</span>
                    </div>
        <div id="think_page_trace_tab_cont" style="overflow:auto;height:212px;padding:0;line-height: 24px">
                        <div style="display:none;">
                <ol style="padding: 0; margin:0">
                    <li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">请求信息 : 2018-01-13 13:20:42 HTTP/1.1 GET : ws.dajiatin.com/index.php/Admin/Admin/js/jquery.purebox.js</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">运行时间 : 0.024854s [ 吞吐率：40.24req/s ] 内存消耗：3,008.70kb 文件加载：49</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">查询信息 : 0 queries 0 writes </li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">缓存信息 : 0 reads,0 writes</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">配置加载 : 93</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">会话信息 : SESSION_ID=em740bl5kcqvmvik6mg5m9ssg1</li>                </ol>
            </div>
                        <div style="display:none;">
                <ol style="padding: 0; margin:0">
                    <li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/index.php ( 2.71 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/start.php ( 0.73 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/base.php ( 2.66 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Loader.php ( 19.47 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/vendor/composer/autoload_namespaces.php ( 0.29 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/vendor/composer/autoload_psr4.php ( 0.89 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/vendor/composer/autoload_classmap.php ( 44.14 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/vendor/composer/autoload_files.php ( 0.43 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/vendor/symfony/polyfill-mbstring/bootstrap.php ( 3.96 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/vendor/topthink/think-captcha/src/helper.php ( 1.59 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Route.php ( 59.52 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Config.php ( 6.03 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Validate.php ( 39.77 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/vendor/topthink/think-testing/src/config.php ( 0.65 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Console.php ( 21.22 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Error.php ( 3.63 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/convention.php ( 10.25 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/App.php ( 21.71 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Request.php ( 49.02 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/config.php ( 14.17 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/database.php ( 1.86 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Hook.php ( 4.76 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/tags.php ( 0.96 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/common.php ( 78.66 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Env.php ( 1.08 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/helper.php ( 22.29 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/function.php ( 24.63 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Lang.php ( 6.93 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Log.php ( 5.72 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/lang/zh-cn.php ( 3.85 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/lang/zh-cn.php ( 0.17 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/route.php ( 1.25 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/admin/config.php ( 1.52 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/admin/common.php ( 16.09 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/admin/controller/Admin.php ( 11.98 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/application/admin/controller/Base.php ( 3.62 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Controller.php ( 7.37 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/traits/controller/Jump.php ( 4.97 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Session.php ( 11.12 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/View.php ( 6.86 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/view/driver/Think.php ( 5.80 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Template.php ( 46.48 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/template/driver/File.php ( 2.24 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Url.php ( 12.71 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/runtime/temp/82fceb730ab35374c189db32a49ce87a.php ( 3.61 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Response.php ( 8.42 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/exception/HttpResponseException.php ( 0.96 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Debug.php ( 7.01 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/debug/Html.php ( 4.27 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Db.php ( 6.42 KB )</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">/data/wwwroot/ws.dajiatin.com/thinkphp/library/think/Cache.php ( 6.35 KB )</li>                </ol>
            </div>
                        <div style="display:none;">
                <ol style="padding: 0; margin:0">
                    <li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">[ LANG ] /data/wwwroot/ws.dajiatin.com/thinkphp/lang/zh-cn.php</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">[ LANG ] /data/wwwroot/ws.dajiatin.com/application/lang/zh-cn.php</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">[ ROUTE ] array (
  'type' =&gt; 'module',
  'module' =&gt; 
  array (
    0 =&gt; 'Admin',
    1 =&gt; 'Admin',
    2 =&gt; 'js',
  ),
)</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">[ HEADER ] array (
  'accept-language' =&gt; 'zh-CN',
  'user-agent' =&gt; 'mozilla/5.0 (windows nt 6.1) applewebkit/537.36 (khtml, like gecko) chrome/34.0.1847.131 safari/537.36',
  'host' =&gt; 'ws.dajiatin.com',
  'content-type' =&gt; '',
  'content-length' =&gt; '',
)</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">[ PARAM ] array (
)</li><li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px">[ VIEW ] ./application/admin/view2/public/dispatch_jump.html [ array (
  0 =&gt; 'action',
  1 =&gt; 'template_now_time',
  2 =&gt; 'code',
  3 =&gt; 'msg',
  4 =&gt; 'data',
  5 =&gt; 'url',
  6 =&gt; 'wait',
) ]</li>                </ol>
            </div>
                        <div style="display:none;">
                <ol style="padding: 0; margin:0">
                                    </ol>
            </div>
                        <div style="display:none;">
                <ol style="padding: 0; margin:0">
                                    </ol>
            </div>
                        <div style="display:none;">
                <ol style="padding: 0; margin:0">
                                    </ol>
            </div>
                    </div>
    </div>
    <div id="think_page_trace_close" style="display:none;text-align:right;height:15px;position:absolute;top:10px;right:12px;cursor:pointer;"><img style="vertical-align:top;" src="data:image/gif;base64,R0lGODlhDwAPAJEAAAAAAAMDA////wAAACH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4wLWMwNjAgNjEuMTM0Nzc3LCAyMDEwLzAyLzEyLTE3OjMyOjAwICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MUQxMjc1MUJCQUJDMTFFMTk0OUVGRjc3QzU4RURFNkEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MUQxMjc1MUNCQUJDMTFFMTk0OUVGRjc3QzU4RURFNkEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoxRDEyNzUxOUJBQkMxMUUxOTQ5RUZGNzdDNThFREU2QSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoxRDEyNzUxQUJBQkMxMUUxOTQ5RUZGNzdDNThFREU2QSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgH//v38+/r5+Pf29fTz8vHw7+7t7Ovq6ejn5uXk4+Lh4N/e3dzb2tnY19bV1NPS0dDPzs3My8rJyMfGxcTDwsHAv769vLu6ubi3trW0s7KxsK+urayrqqmop6alpKOioaCfnp2cm5qZmJeWlZSTkpGQj46NjIuKiYiHhoWEg4KBgH9+fXx7enl4d3Z1dHNycXBvbm1sa2ppaGdmZWRjYmFgX15dXFtaWVhXVlVUU1JRUE9OTUxLSklIR0ZFRENCQUA/Pj08Ozo5ODc2NTQzMjEwLy4tLCsqKSgnJiUkIyIhIB8eHRwbGhkYFxYVFBMSERAPDg0MCwoJCAcGBQQDAgEAACH5BAAAAAAALAAAAAAPAA8AAAIdjI6JZqotoJPR1fnsgRR3C2jZl3Ai9aWZZooV+RQAOw==" /></div>
</div>
<div id="think_page_trace_open" style="height:30px;float:right;text-align:right;overflow:hidden;position:fixed;bottom:0;right:0;color:#000;line-height:30px;cursor:pointer;">
    <div style="background:#232323;color:#FFF;padding:0 6px;float:right;line-height:30px;font-size:14px">0.025793s </div>
    <img width="30" style="" title="ShowPageTrace" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjVERDVENkZGQjkyNDExRTE5REY3RDQ5RTQ2RTRDQUJCIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjVERDVENzAwQjkyNDExRTE5REY3RDQ5RTQ2RTRDQUJCIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NURENUQ2RkRCOTI0MTFFMTlERjdENDlFNDZFNENBQkIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NURENUQ2RkVCOTI0MTFFMTlERjdENDlFNDZFNENBQkIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5fx6IRAAAMCElEQVR42sxae3BU1Rk/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">
</div>

<script type="text/javascript">
    (function(){
        var tab_tit  = document.getElementById('think_page_trace_tab_tit').getElementsByTagName('span');
        var tab_cont = document.getElementById('think_page_trace_tab_cont').getElementsByTagName('div');
        var open     = document.getElementById('think_page_trace_open');
        var close    = document.getElementById('think_page_trace_close').children[0];
        var trace    = document.getElementById('think_page_trace_tab');
        var cookie   = document.cookie.match(/thinkphp_show_page_trace=(\d\|\d)/);
        var history  = (cookie && typeof cookie[1] != 'undefined' && cookie[1].split('|')) || [0,0];
        open.onclick = function(){
            trace.style.display = 'block';
            this.style.display = 'none';
            close.parentNode.style.display = 'block';
            history[0] = 1;
            document.cookie = 'thinkphp_show_page_trace='+history.join('|')
        }
        close.onclick = function(){
            trace.style.display = 'none';
            this.parentNode.style.display = 'none';
            open.style.display = 'block';
            history[0] = 0;
            document.cookie = 'thinkphp_show_page_trace='+history.join('|')
        }
        for(var i = 0; i < tab_tit.length; i++){
            tab_tit[i].onclick = (function(i){
                return function(){
                    for(var j = 0; j < tab_cont.length; j++){
                        tab_cont[j].style.display = 'none';
                        tab_tit[j].style.color = '#999';
                    }
                    tab_cont[i].style.display = 'block';
                    tab_tit[i].style.color = '#000';
                    history[1] = i;
                    document.cookie = 'thinkphp_show_page_trace='+history.join('|')
                }
            })(i)
        }
        parseInt(history[0]) && open.click();
        tab_tit[history[1]].click();
    })();
</script>
</body>
</html>