/*
*******************************************************************************************
FONCTIONS COMMUNES
*******************************************************************************************
*/

	jQuery(document).ready(function() 
	{
		// gestion des infobulles
		if(jQuery('.tipsyer').length>0){
			jQuery('.tipsyer').tipsy({gravity: 's',fade: true,delayIn: 100,delayOut: 100,html: true});
		}
	}
	);
	
	// gestion onglets
	jQuery('.cs_heading_elt').live('click',function()
	{
		var oI = jQuery(this).attr('id').split('_')[1];
		jQuery(this).parent().find('.cs_heading_elt').removeClass('active');
		jQuery(this).parent().find('#elt_' + oI).addClass('active');
		jQuery(this).parent().parent().parent().find('.toggelize').css('display','none');
		jQuery(this).parent().parent().parent().find('#referred_' + oI).css('display','block');	
	}
	);

	// gestion des sous menus
	jQuery('.cs_sub-menu-item').live('click',function()
	{
		var oI = jQuery(this).find('a').attr('id');
		jQuery(this).parent().find('.cs_sub-menu-item').find('a').removeClass('active');
		jQuery(this).find('a').addClass('active');
		jQuery('.container').css('display','none');
		jQuery('.form_wrapper').prepend('<div class="loading_temp"><div class="loading_spinner"><img src="../wp-content/plugins/cheetahmail/img/layer-loader.gif" /></div></div>');
		
		setTimeout(function(){

			jQuery('.loading_temp').remove();
			jQuery('.c_' + oI).css('display','block');
			write_subtitle();
		},500);

	}
	);	
		
/*
*******************************************************************************************
FONCTIONS GROWL
*******************************************************************************************
*/

	// function afficher
	function cs_show_growl(type,msg)
	{
		if(jQuery('.growl').length==0)
		{	
			var valoris = '';
			valoris += '<div class="growl" style="display:none">';
				valoris += '<h4';
				if(type==1){
					// information
					valoris += ' class="informationning"> ' + information;
				}else if( type == 2){
					// success
					valoris += ' class="successing"> ' + success;				
				} else if( type ==3){
					// error
					valoris += ' class="alerting"> ' + error;
				}
				valoris += '</h4>';
				valoris += '<p>';
					valoris += msg;
				valoris += '</p>';
			valoris += '</div>';			
			jQuery('body').append(valoris);
			jQuery('.growl').slideDown(400);
			var t = setTimeout("cs_hide_growl()",5000);
		}
	}	

	// function desaffichage
	function cs_hide_growl()
	{
		if(jQuery('.growl').length>0){
			jQuery('.growl').slideUp(600,function(){
				jQuery(this).remove();
			});
		}
	}		

/*
*******************************************************************************************
FONCTION DE MAJ DES SETTINGS DU PLUGIN
*******************************************************************************************
*/

jQuery(document).ready(function(){
	if(jQuery('#mailer_mail:checked').length>0){
		jQuery('#elt_2').fadeOut('fast');
		jQuery('#layout_smtp').fadeOut('fast');
	}else{
		// on désaffiche l'onglet SMTP
		jQuery('#elt_2').fadeIn('fast');
		jQuery('#layout_smtp').fadeIn('fast');	
	}
});
// function mise à jour des infos du plugin

jQuery('#mailer_mail').live('click',function()
{
	// on désaffiche l'onglet SMTP
	jQuery('#elt_2').fadeOut('fast');
	jQuery('#layout_smtp').fadeOut('fast');
}
);


jQuery('#mailer_smtp').live('click',function()
{
	// on désaffiche l'onglet SMTP
	jQuery('#elt_2').fadeIn('fast');
	jQuery('#layout_smtp').fadeIn('fast');	
}
);

jQuery('.cs_save_settings').live('click',function()
{
	// on recupère les valeurs
	if(jQuery('#mailer_mail:checked').length>0){
		var mailer = 'mail';
	}else{
		var mailer = 'smtp';
	}
		

	var mail_set_return_path = jQuery('#mail_set_return_path').val();
	var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
	
	if(mailer == 'mail'){
		// cas wp_mail classique
		var mail_from = '';
		var mail_from_name = '';
		var smtp_host = 'localhost';
		var smtp_port = '25';	
		var smtp_auth = false;
		var smtp_user = '';
		var smtp_pass = '';	
		var smtp_ssl = 'none';		
	}else{
		// cas wp_mail SMTP
		var mail_from = jQuery('#mail_from').val();
		if(!reg.test(mail_from)){
			mail_from = '';
		}
		var mail_from_name = jQuery('#mail_from_name').val();		
		var smtp_host = jQuery('#smtp_host').val();
		var smtp_port = jQuery('#smtp_port').val();	
		var smtp_auth = true;
		var smtp_user = jQuery('#smtp_user').val();
		var smtp_pass = jQuery('#smtp_pass').val();		
		
		if(jQuery('#smtp_ssl_ssl:checked').length>0){
			var smtp_ssl = 'ssl';
		}else if(jQuery('#smtp_ssl_tls:checked').length>0){
			var smtp_ssl = 'tls';
		}else{
			var smtp_ssl = 'none';
		}
	}
	
	// on teste tous les paramètres
	if(
		mailer.length > 0 &&
		mail_set_return_path.length > 0
	){	
		jQuery(this).parent().fadeOut('fast');
		jQuery(this).parent().parent().append('<div class="loading_spinner"><img src="../wp-content/plugins/cheetahmail/img/layer-loader.gif" /></div>');	
		
		jQuery.ajax({
			type: "POST",
			url: "../wp-content/plugins/cheetahsender/ajax/save_settings.php",
			data: { 
				mail_from:mail_from, 
				mail_from_name:mail_from_name, 
				mailer:mailer,
				mail_set_return_path:mail_set_return_path,
				smtp_host:smtp_host,			
				smtp_port:smtp_port,			
				smtp_ssl:smtp_ssl,
				smtp_auth:smtp_auth,
				smtp_user:smtp_user,
				smtp_pass:smtp_pass
			},
			 success: function(data){
				
				if(data == -1 || data == -2){
					// erreur WS
					cs_show_growl(3,settings_params_error);
				}else if(data == 0){					
					// SUCCESS
					cs_show_growl(2,settings_success);
				}			
				jQuery('.loading_spinner').remove();
				jQuery('.cs_save_settings').parent().fadeIn('fast');
				if(mailer == 'mail'){
					setTimeout("document.location.reload()",2000);
				}
			} // fin success
		});
	}else{
		// param manquant
		cs_show_growl(3,global_error);
	}
}
);





jQuery('#cs_send_bat').live('click',function()
{	
	var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
	var to = jQuery('#to').val();	
	if(reg.test(to)){		
	// email ok
	jQuery(this).parent().fadeOut('fast');
	jQuery(this).parent().parent().append('<div class="loading_spinner"><img src="../wp-content/plugins/cheetahmail/img/layer-loader.gif" /></div>');		
	jQuery.ajax({
		type: "POST",
		url: "../wp-content/plugins/cheetahsender/ajax/send_test_mail.php",
		data: { 
			to:to
		},
		 success: function(data){
			if(data == 0){
				cs_show_growl(2,bat_success);				
			}if(data == -1){
				cs_show_growl(3,bat_error);				
			}else{
				cs_show_growl(3,data);				
			}
			jQuery('.loading_spinner').remove();
			jQuery('#cs_send_bat').parent().fadeIn('fast');
		}// fin success
	});	

}else{
	cs_show_growl(3,bat_email_wrong);	
}
	
	
}
);