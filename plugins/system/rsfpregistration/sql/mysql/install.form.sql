INSERT INTO `#__rsform_forms` SET
`FormName`='rsform-pro-registration-form',
`FormLayout`='<h2>{global:formtitle}</h2>\r\n{error}\r\n<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->\r\n<fieldset class="formHorizontal formContainer" id="rsform_{global:formid}_page_0">\r\n	<div class="rsform-block rsform-block-name">\r\n	<div class="formControlLabel">{name:caption}</div>\r\n		<div class="formControls">\r\n			<div class="formBody">{name:body}<span class="formValidation">{name:validation}</span></div>\r\n			<p class="formDescription">{name:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-username">\r\n	<div class="formControlLabel">{username:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n			<div class="formBody">{username:body}<span class="formValidation">{username:validation}</span></div>\r\n			<p class="formDescription">{username:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-email">\r\n	<div class="formControlLabel">{email:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n			<div class="formBody">{email:body}<span class="formValidation">{email:validation}</span></div>\r\n			<p class="formDescription">{email:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-verifyemail">\r\n	<div class="formControlLabel">{verifyemail:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n			<div class="formBody">{verifyemail:body}<span class="formValidation">{verifyemail:validation}</span></div>\r\n			<p class="formDescription">{verifyemail:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-password">\r\n	<div class="formControlLabel">{password:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n			<div class="formBody">{password:body}<span class="formValidation">{password:validation}</span></div>\r\n			<p class="formDescription">{password:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-verifypassword">\r\n	<div class="formControlLabel">{verifypassword:caption}<strong class="formRequired">(*)</strong></div>\r\n		<div class="formControls">\r\n			<div class="formBody">{verifypassword:body}<span class="formValidation">{verifypassword:validation}</span></div>\r\n			<p class="formDescription">{verifypassword:description}</p>\r\n		</div>\r\n	</div>\r\n	<div class="rsform-block rsform-block-register">\r\n	<div class="formControlLabel">{register:caption}</div>\r\n		<div class="formControls">\r\n			<div class="formBody">{register:body}<span class="formValidation">{register:validation}</span></div>\r\n			<p class="formDescription">{register:description}</p>\r\n		</div>\r\n	</div>\r\n</fieldset>\r\n',
`FormLayoutName`='responsive',
`FormLayoutAutogenerate`=1,
`FormTitle`='RSForm! Pro Registration Form',
`Published`=1,
`Lang`='',
`ReturnUrl`='',
`Thankyou`='<p>Thank you for your submission! We will contact you as soon as possible.</p>',
`UserEmailFrom`='{global:mailfrom}',
`UserEmailFromName`='{global:fromname}',
`UserEmailMode`=1,
`AdminEmailMode`=1,
`Keepdata`=1,
`ErrorMessage`='<p class="formRed">Please complete all required fields!</p>',
`MultipleSeparator`='\n';

SET @formId = LAST_INSERT_ID();

/* the name field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 1, 1, 1);

SET @componentIdName = LAST_INSERT_ID();

/* the username field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 1, 2, 1);

SET @componentIdUsername = LAST_INSERT_ID();

/* the email field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 1, 3, 1);

SET @componentIdEmail = LAST_INSERT_ID();

/* the verifyemail field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 1, 4, 1);

SET @componentIdVerifyEmail = LAST_INSERT_ID();

/* the password field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 14, 5, 1);

SET @componentIdPassword = LAST_INSERT_ID();

/* the verifypassword field */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 14, 6, 1);

SET @componentIdVerifyPassword = LAST_INSERT_ID();

/* the register submit button */
INSERT INTO `#__rsform_components`
(`FormId`, `ComponentTypeId`, `Order`, `Published`)
VALUES
(@formId, 13, 7, 1);

SET @componentIdRegister = LAST_INSERT_ID();

INSERT INTO `#__rsform_properties` (`ComponentId`, `PropertyName`, `PropertyValue`) VALUES
(@componentIdName, 'NAME', 'name'),
(@componentIdName, 'CAPTION', 'Name'),
(@componentIdName, 'DEFAULTVALUE', ''),
(@componentIdName, 'DESCRIPTION', ''),
(@componentIdName, 'REQUIRED', 'YES'),
(@componentIdName, 'VALIDATIONRULE', 'none'),
(@componentIdName, 'VALIDATIONEXTRA', ''),
(@componentIdName, 'VALIDATIONMESSAGE', 'Please enter the name!'),
(@componentIdName, 'INPUTTYPE', 'text'),
(@componentIdName, 'SIZE', '20'),
(@componentIdName, 'MAXSIZE', ''),
(@componentIdName, 'PLACEHOLDER', ''),
(@componentIdName, 'ADDITIONALATTRIBUTES', ''),
(@componentIdName, 'EMAILATTACH', ''),
(@componentIdUsername, 'NAME', 'username'),
(@componentIdUsername, 'CAPTION', 'Username'),
(@componentIdUsername, 'DEFAULTVALUE', ''),
(@componentIdUsername, 'DESCRIPTION', ''),
(@componentIdUsername, 'REQUIRED', 'YES'),
(@componentIdUsername, 'VALIDATIONRULE', 'none'),
(@componentIdUsername, 'VALIDATIONEXTRA', ''),
(@componentIdUsername, 'VALIDATIONMESSAGE', 'Please provide an username!'),
(@componentIdUsername, 'INPUTTYPE', 'text'),
(@componentIdUsername, 'SIZE', '20'),
(@componentIdUsername, 'MAXSIZE', ''),
(@componentIdUsername, 'PLACEHOLDER', ''),
(@componentIdUsername, 'ADDITIONALATTRIBUTES', ''),
(@componentIdUsername, 'EMAILATTACH', ''),
(@componentIdEmail, 'NAME', 'email'),
(@componentIdEmail, 'CAPTION', 'E-mail'),
(@componentIdEmail, 'DEFAULTVALUE', ''),
(@componentIdEmail, 'DESCRIPTION', ''),
(@componentIdEmail, 'REQUIRED', 'YES'),
(@componentIdEmail, 'VALIDATIONRULE', 'email'),
(@componentIdEmail, 'VALIDATIONEXTRA', ''),
(@componentIdEmail, 'VALIDATIONMESSAGE', 'Please provide a valid e-mail!'),
(@componentIdEmail, 'INPUTTYPE', 'email'),
(@componentIdEmail, 'SIZE', '20'),
(@componentIdEmail, 'MAXSIZE', ''),
(@componentIdEmail, 'PLACEHOLDER', ''),
(@componentIdEmail, 'ADDITIONALATTRIBUTES', ''),
(@componentIdEmail, 'EMAILATTACH', ''),
(@componentIdVerifyEmail, 'NAME', 'verifyemail'),
(@componentIdVerifyEmail, 'CAPTION', 'Verify E-mail'),
(@componentIdVerifyEmail, 'DEFAULTVALUE', ''),
(@componentIdVerifyEmail, 'DESCRIPTION', ''),
(@componentIdVerifyEmail, 'REQUIRED', 'YES'),
(@componentIdVerifyEmail, 'VALIDATIONRULE', 'email'),
(@componentIdVerifyEmail, 'VALIDATIONEXTRA', ''),
(@componentIdVerifyEmail, 'VALIDATIONMESSAGE', 'Retype the e-mail!'),
(@componentIdVerifyEmail, 'INPUTTYPE', 'email'),
(@componentIdVerifyEmail, 'SIZE', '20'),
(@componentIdVerifyEmail, 'MAXSIZE', ''),
(@componentIdVerifyEmail, 'PLACEHOLDER', ''),
(@componentIdVerifyEmail, 'ADDITIONALATTRIBUTES', ''),
(@componentIdVerifyEmail, 'EMAILATTACH', ''),
(@componentIdPassword, 'NAME', 'password'),
(@componentIdPassword, 'CAPTION', 'Password'),
(@componentIdPassword, 'DEFAULTVALUE', ''),
(@componentIdPassword, 'DESCRIPTION', ''),
(@componentIdPassword, 'REQUIRED', 'YES'),
(@componentIdPassword, 'VALIDATIONEXTRA', ''),
(@componentIdPassword, 'VALIDATIONRULE', 'none'),
(@componentIdPassword, 'VALIDATIONMESSAGE', 'Please enter a password!'),
(@componentIdPassword, 'SIZE', ''),
(@componentIdPassword, 'MAXSIZE', ''),
(@componentIdPassword, 'PLACEHOLDER', ''),
(@componentIdPassword, 'ADDITIONALATTRIBUTES', ''),
(@componentIdPassword, 'EMAILATTACH', ''),
(@componentIdVerifyPassword, 'NAME', 'verifypassword'),
(@componentIdVerifyPassword, 'CAPTION', 'Verify Password'),
(@componentIdVerifyPassword, 'DEFAULTVALUE', ''),
(@componentIdVerifyPassword, 'DESCRIPTION', ''),
(@componentIdVerifyPassword, 'REQUIRED', 'YES'),
(@componentIdVerifyPassword, 'VALIDATIONEXTRA', 'password'),
(@componentIdVerifyPassword, 'VALIDATIONRULE', 'sameas'),
(@componentIdVerifyPassword, 'VALIDATIONMESSAGE', 'Retype the password!'),
(@componentIdVerifyPassword, 'SIZE', ''),
(@componentIdVerifyPassword, 'MAXSIZE', ''),
(@componentIdVerifyPassword, 'PLACEHOLDER', ''),
(@componentIdVerifyPassword, 'ADDITIONALATTRIBUTES', ''),
(@componentIdVerifyPassword, 'EMAILATTACH', ''),
(@componentIdRegister, 'NAME', 'register'),
(@componentIdRegister, 'LABEL', 'Register'),
(@componentIdRegister, 'CAPTION', ''),
(@componentIdRegister, 'RESET', 'NO'),
(@componentIdRegister, 'RESETLABEL', ''),
(@componentIdRegister, 'DISPLAYPROGRESSMSG', '<div>\r\n <p><em>Page <strong>{page}</strong> of {total}</em></p>\r\n <div class="rsformProgressContainer">\r\n  <div class="rsformProgressBar" style="width: {percent}%;"></div>\r\n </div>\r\n</div>'),
(@componentIdRegister, 'PREVBUTTON', 'PREV'),
(@componentIdRegister, 'DISPLAYPROGRESS', 'NO'),
(@componentIdRegister, 'BUTTONTYPE', 'TYPEINPUT'),
(@componentIdRegister, 'ADDITIONALATTRIBUTES', ''),
(@componentIdRegister, 'EMAILATTACH', '');

INSERT INTO `#__rsform_registration` (`form_id`, `itemid`, `action`, `action_field`, `vars`, `groups`, `activation`, `cbactivation`, `defer_admin_email`, `user_activation_action`, `admin_activation_action`, `user_activation_url`, `admin_activation_url`, `user_activation_text`, `admin_activation_text`, `password_strength`,  `published`) VALUES
(@formId, 0, 1, '', 'a:6:{s:4:"name";s:4:"name";s:8:"username";s:8:"username";s:6:"email1";s:5:"email";s:6:"email2";s:11:"verifyemail";s:9:"password1";s:8:"password";s:9:"password2";s:14:"verifypassword";}', '2', 0, 1, 0, 0, 0, '', '', '', '', 1, 1);

UPDATE `#__rsform_config` SET `SettingValue` = @formId WHERE `#__rsform_config`.`SettingName` = 'registration_form';