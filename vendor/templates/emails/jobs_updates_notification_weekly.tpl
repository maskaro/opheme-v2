<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 20px 0px 0px;" align="left" valign="top">
      <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="text-pad" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 30px 30px;" align="left" valign="top">
            <p class="lead" style="color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 21px; font-size: 18px; margin: 0 0 10px; padding: 0;" align="left">Hi, {$Data.email.firstName}!</p>
            <p style="color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">Here's a recap of what {$Data.email.brand}'s been up to this past week on your behalf:</p>
          </td>
          <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
        </tr></table></td>
  </tr></table>
{if isset($Data.email.jobs)}
<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
    <td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 20px 0px 0px;" align="left" valign="top">
      <table class="twelve columns last" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="center" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 0px 30px;" align="center" valign="top">
            <center style="width: 100%; min-width: 280px;">
              <p class="lead yellow-text" style="text-align: center; color: #f1c40f; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 21px; font-size: 18px; margin: 0 0 10px; padding: 0;" align="center">Total New Messages</p>
              <p class="yellow-text" style="text-align: center; color: #f1c40f; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="center">
                {foreach $Data.email.jobs as $jobsType => $jobs}
                  {foreach $jobs as $job}
                    {$job.messages}<br />
                  {/foreach}
                {/foreach}
              </p>
            </center>
          </td>
          <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
        </tr></table></td>
  </tr></table>
{/if}

{if isset($Data.email.followInfo)}
<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 20px 0px 0px;" align="left" valign="top">
      <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="text-pad" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 30px 30px;" align="left" valign="top">
            <p class="lead blue-text" style="color: #3498db; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 21px; font-size: 18px; margin: 0 0 10px; padding: 0;" align="left">
              These people Followed you:
            </p>
            <p style="color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
              {foreach $Data.email.followInfo as $follow}
              <span class="blue-text" style="color: #3498db;">{ucfirst($follow.authKeyType)}</span> - <span class="yellow-text" style="color: #f1c40f;">{ucfirst($follow.sm_user_screen_name)}</span>
              </p><hr style="color: #d9d9d9; height: 1px; background: #d9d9d9; border: none;" /><span class="blue-text" style="color: #3498db;">{ucfirst($follow.authKeyType)}</span> - <span class="yellow-text" style="color: #f1c40f;">{ucfirst($follow.sm_user_screen_name)}</span>
              <hr style="color: #d9d9d9; height: 1px; background: #d9d9d9; border: none;" />
              {/foreach}
            
          </td>
          <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
        </tr></table></td>
  </tr></table>
{/if}

{if isset($Data.email.replyInfo)}
<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 20px 0px 0px;" align="left" valign="top">
      <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="text-pad" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 30px 30px;" align="left" valign="top">
            <p class="lead blue-text" style="color: #3498db; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 21px; font-size: 18px; margin: 0 0 10px; padding: 0;" align="left">
              These people replied to you:
            </p>
            <p style="color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
              {foreach $Data.email.replyInfo as $reply}
              <span class="blue-text" style="color: #3498db;">{ucfirst($reply.authKeyType)}</span> - <span class="yellow-text" style="color: #f1c40f;">@{$reply.sm_user_screen_name}</span>
              </p><hr style="color: #d9d9d9; height: 1px; background: #d9d9d9; border: none;" /><span class="blue-text" style="color: #3498db;">{ucfirst($reply.authKeyType)}</span> - <span class="yellow-text" style="color: #f1c40f;">@{$reply.sm_user_screen_name}</span>
              <hr style="color: #d9d9d9; height: 1px; background: #d9d9d9; border: none;" />
              {/foreach}
            
          </td>
          <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
        </tr></table></td>
  </tr></table>
{/if}

{if isset($Data.email.favouritedInfo)}
<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 20px 0px 0px;" align="left" valign="top">
      <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="text-pad" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 30px 30px;" align="left" valign="top">
            <p class="lead blue-text" style="color: #3498db; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 21px; font-size: 18px; margin: 0 0 10px; padding: 0;" align="left">
              Your message was Favourited:
            </p>
            <p style="color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">
              {foreach $Data.email.favouritedInfo as $favourite}
              <span class="blue-text" style="color: #3498db;">{ucfirst($favourite.authKeyType)}</span> - <span class="yellow-text" style="color: #f1c40f;">{$favourite.message}</span>
              </p><hr style="color: #d9d9d9; height: 1px; background: #d9d9d9; border: none;" /><span class="blue-text" style="color: #3498db;">{ucfirst($favourite.authKeyType)}</span> - <span class="yellow-text" style="color: #f1c40f;">{$favourite.message}</span>
              <hr style="color: #d9d9d9; height: 1px; background: #d9d9d9; border: none;" />
              {/foreach}
            
          </td>
          <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
        </tr></table></td>
  </tr></table>
{/if}

<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 20px 0px 0px;" align="left" valign="top">
      <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;"><tr style="vertical-align: top; text-align: left; padding: 0;" align="left"><td class="text-pad" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 30px 30px;" align="left" valign="top">
            <p style="color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">Find out much more by logging into your {$Data.email.brand} account at <a href="{$Data.email.loginUrl}" target="_blank" style="color: #3498db; text-decoration: none;">{$Data.email.loginUrl}</a>.
              <br /><br /><br />
              Until next time,
              <br />
              The {$Data.email.brand} Team
              <br /><br /><br />
              PS: You can control the frequency of this email notification in your {$Data.email.brand} Dashboard area.
            </p>
          </td>
          <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #494949; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="top"></td>
        </tr></table></td>
  </tr></table>