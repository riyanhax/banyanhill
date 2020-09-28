jQuery(document).ajaxComplete(function(){jQuery(document).off("click",".csd_ext_auto_renewal_change"),jQuery(document).on("click",".csd_ext_auto_renewal_change",function(e){jQuery(".featherlight-close").click(),e.preventDefault(),data={},1==jQuery(this).data("lifetime")?(data.lifetime=jQuery(this).data("lifetime"),openCsdExtPopup("csd_ext_lifetime",data)):(data.auto_status=jQuery(this).data("auto"),data.subref=jQuery(this).data("subref"),data.expire=jQuery(this).data("expire"),data.subname=jQuery(this).data("subname"),openCsdExtPopup("csd_ext_change_auto_renew",data))}),jQuery("#csd_ext_auto_renew_confirm").click(function(){jQuery(".featherlight-close").click(),data={},data.auto_status=jQuery(this).data("auto"),data.subref=jQuery(this).data("subref"),data.expire=jQuery(this).data("expire"),data.subname=jQuery(this).data("subname"),openCsdExtPopup("csd_ext_change_auto_renew_confirm",data)}),jQuery("#csd_ext_auto_renew_remind").click(function(){jQuery(".featherlight-close").click(),data={},data.sub_ref=jQuery(this).data("subref"),data.expire=jQuery(this).data("expire"),data.sub_name=jQuery(this).data("subname"),openCsdExtPopup("csd_ext_auto_renew_remind",data)})}),jQuery(document).ajaxComplete(function(){jQuery(document).off("click",".csd_ext_email_change"),jQuery(document).on("click",".csd_ext_email_change",function(e){e.preventDefault(),data={},data.sub_ref=jQuery(this).data("subref"),data.old_email=jQuery(this).data("subs-email"),openCsdExtPopup("csd_ext_change_email_address",data)}),jQuery("#csd_ext_email_change_confirm").click(function(e){e.preventDefault(),jQuery("#csd_ext_change_email_form").valid()&&(jQuery(".featherlight-close").click(),data={},data.sub_ref=jQuery("#csd_ext_submit_subref").val(),data.new_email=jQuery("#csd_ext_new_email").val(),data.new_email_repeat=jQuery("#csd_ext_new_email_repeat").val(),openCsdExtPopup("csd_ext_change_email_address_confirm",data))})}),jQuery(document).change(function(){jQuery("#csd_ext_change_email_form").validate({rules:{csd_ext_new_email:{required:!0,email:!0},csd_ext_new_email_repeat:{equalTo:'[name="csd_ext_new_email"]'}},messages:{csd_ext_new_email:{required:"Please enter a valid email address"},csd_ext_new_email_repeat:{equalTo:"These emails do not match"}}})}),jQuery(document).ajaxComplete(function(){jQuery(document).off("click",".csd_ext_renewal_date_change"),jQuery(document).on("click",".csd_ext_renewal_date_change",function(e){e.preventDefault(),data={},1==jQuery(this).data("lifetime")?(data.lifetime=jQuery(this).data("lifetime"),openCsdExtPopup("csd_ext_lifetime",data)):(data.url=jQuery(this).data("url"),data.savings=jQuery(this).data("savings"),openCsdExtPopup("csd_ext_change_renewal_date",data))}),jQuery("#csd_ext_renewal_date_confirm").click(function(){var e=jQuery(this).data("url");window.open(e,"_blank"),jQuery(".featherlight-close").click()})}),jQuery(document).ajaxComplete(function(){jQuery(document).off("click",".csd_ext_renewal_price_change"),jQuery(document).on("click",".csd_ext_renewal_price_change",function(e){e.preventDefault(),data={},1==jQuery(this).data("lifetime")?(data.lifetime=jQuery(this).data("lifetime"),openCsdExtPopup("csd_ext_lifetime",data)):(data.url=jQuery(this).data("url"),data.rate=jQuery(this).data("rate"),data.price=jQuery(this).data("price"),openCsdExtPopup("csd_ext_change_renewal_price",data))}),jQuery("#csd_ext_renewal_price_confirm").click(function(){var e=jQuery(this).data("url");window.open(e,"_blank"),jQuery(".featherlight-close").click()})}),jQuery(document).ajaxComplete(function(){if(jQuery(document).off("click",".csd_ext_status_change"),jQuery(document).on("click",".csd_ext_status_change",function(e){e.preventDefault(),data={},data.email_address=jQuery(this).data("listemail"), data.reports_link=jQuery(this).data("reportslinkid"),data.sub_ref=jQuery(this).data("subref"),data.auto_status=jQuery(this).data("auto"),data.post_id=jQuery(this).data("postid"),data.lifetime=jQuery(this).data("lifetime"),data.pubcode=jQuery(this).data("pubcode"),data.expire=jQuery(this).data("expire"),data.subname=jQuery(this).data("subname"),openCsdExtPopup("csd_ext_change_status",data)}),jQuery("#csd_ext_status_change_next").on("click",function(){data={},data.status_flow_index=jQuery("#csd_ext_status_flow_index").val(),data.sub_ref=jQuery(this).data("subref"),data.post_id=jQuery(this).data("postid"),data.lifetime=jQuery(this).data("lifetime"),data.pubcode=jQuery(this).data("pubcode"),jQuery(".featherlight-close").click(),openCsdExtPopup("csd_ext_change_status",data)}),jQuery("#csd_ext_pause_status").click(function(){jQuery(".featherlight-close").click(),data={},data.sub_ref=jQuery(this).data("subref"),openCsdExtPopup("csd_ext_pause_status",data)}),jQuery("#csd_ext_status_refund").click(function(){jQuery(".featherlight-close").click(),data={},data.sub_ref=jQuery(this).data("subref"),openCsdExtPopup("csd_ext_status_refund",data)}),jQuery("#csd_ext_status_end").click(function(){jQuery(".featherlight-close").click()}),jQuery('input[name="video_proceed"]').length){var e=jQuery('input[name="video_proceed"]').val();setTimeout(function(){jQuery(".wait").removeClass("wait")},1e3*e)}}),jQuery(document).ajaxComplete(function(){jQuery(document).off("click",".csd_ext_text_alert_change"),jQuery(document).on("click",".csd_ext_text_alert_change",function(e){e.preventDefault(),data={},data.sub_ref=jQuery(this).data("subref"),data.phone=jQuery(this).data("phone"),data.addr_code=jQuery(this).data("addrcode"),openCsdExtPopup("csd_ext_change_text_alert",data)}),jQuery("#csd_ext_text_alert_change_confirm").click(function(e){e.preventDefault(),jQuery("#csd_ext_change_text_alert_form").valid()&&(jQuery(".featherlight-close").click(),data={},data.sub_ref=jQuery(this).data("subref"),data.new_phone=jQuery("#csd_ext_new_phone").val(),data.new_phone_repeat=jQuery("#csd_ext_new_phone_repeat").val(),data.addr_code=jQuery(this).data("addrcode"),openCsdExtPopup("csd_ext_change_text_alert_confirm",data))}),jQuery.validator.addMethod("textAlertRegEx",function(e,t,a){return a.test(e)},"Please enter a valid phone number")}),jQuery(document).change(function(){jQuery("#csd_ext_change_text_alert_form").validate({rules:{csd_ext_new_phone:{textAlertRegEx:/^(?=.*[0-9])[- +()0-9]+$|^$/},csd_ext_new_phone_repeat:{equalTo:'[name="csd_ext_new_phone"]'}},messages:{csd_ext_new_phone:"Please enter a valid phone number",csd_ext_new_phone_repeat:{equalTo:"These phone numbers do not match"}}})}),openCsdExtPopup=function(e,t){jQuery.featherlight(jQuery("#csd_ext_modal"),{}),jQuery.ajax({url:csd_ext_js_localize_frontend.csd_ext_ajax_url,type:"POST",data:{data:t,action:e},beforeSend:function(e){jQuery(".featherlight-inner").html("<div class='tfs_css_preloader'><img src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDBweCIgIGhlaWdodD0iNDBweCIgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIiBjbGFzcz0ibGRzLWVjbGlwc2UiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsgYmFja2dyb3VuZDogbm9uZTsiPjxwYXRoIG5nLWF0dHItZD0ie3tjb25maWcucGF0aENtZH19IiBuZy1hdHRyLWZpbGw9Int7Y29uZmlnLmNvbG9yfX0iIHN0cm9rZT0ibm9uZSIgZD0iTTEwIDUwQTQwIDQwIDAgMCAwIDkwIDUwQTQwIDQzIDAgMCAxIDEwIDUwIiBmaWxsPSJyZ2JhKDAlLDAlLDAlLDAuNikiIHRyYW5zZm9ybT0icm90YXRlKDM2MCAtOC4xMDg3OGUtOCAtOC4xMDg3OGUtOCkiIGNsYXNzPSIiIHN0eWxlPSJhbmltYXRpb24tcGxheS1zdGF0ZTogcnVubmluZzsgYW5pbWF0aW9uLWRlbGF5OiAwczsiPjxhbmltYXRlVHJhbnNmb3JtIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgdHlwZT0icm90YXRlIiBjYWxjTW9kZT0ibGluZWFyIiB2YWx1ZXM9IjAgNTAgNTEuNTszNjAgNTAgNTEuNSIga2V5VGltZXM9IjA7MSIgZHVyPSIwLjVzIiBiZWdpbj0iMHMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIiBjbGFzcz0iIiBzdHlsZT0iYW5pbWF0aW9uLXBsYXktc3RhdGU6IHJ1bm5pbmc7IGFuaW1hdGlvbi1kZWxheTogMHM7Ij48L2FuaW1hdGVUcmFuc2Zvcm0+PC9wYXRoPjwvc3ZnPg==' alt='Loading' class='loading'></div>")},success:function(e){e=JSON.parse(e),jQuery(".featherlight-inner").html(e.html),e.email_address&&(jQuery("."+e.sub_ref+"-email").parents("article[data-list-pubcode]").find("#email").html(e.email_address),jQuery("."+e.sub_ref+"-email_button").remove()),e.auto_renew_change&&(jQuery("."+e.sub_ref+"-auto_renew").html(e.auto_renew_change),jQuery("."+e.sub_ref+"-auto_renew_button").data("auto",e.auto_renew_change)),e.new_status&&(jQuery("."+e.sub_ref+"-status").html(e.new_status),jQuery("."+e.sub_ref+"-button").hide()),e.phone&&jQuery(".text-alert-phone").html(e.phone)},error:function(e){jQuery(".featherlight-inner").html(e)}})},jQuery(document).ready(function(){jQuery("#tfs_css_header").length&&(jQuery(".navigation-wrap").css("z-index","9"),jQuery(".col-lg-9").css("z-index","8"))});