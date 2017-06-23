<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 20px 0px 0px;" align="left" valign="top">
      <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="text-pad" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 30px 30px;" align="left" valign="top">
            <p style="color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
              Thank you for signing up! We're absolutely sure you'll enjoy using {$Data.company.brand}.<br />
            </p>
            <p style="color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
              Please access the following link (or copy it in your browser if for whatever reason you cannot click it) to use your new account:
              <br /><br /><a href="{$Data.email.loginUrl}" target="_blank" style="color: #3498db; text-decoration: none;">{$Data.email.loginUrl}</a>
              <br /><br />
              Username: {$Data.email.email}
              <br />
              Password: <strong>{if isset($Data.email.regToken)}{$Data.email.regToken}{else}(use the one You supplied us on the Registration page){/if}</strong>
              <br /><br /><br />
              Thanks
              <br /><br />
              The {$Data.company.brand} Team
            </p>
          </td>
          <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
        </tr></table></td>
  </tr></table>