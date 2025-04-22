<!DOCTYPE html>
<html>
    <head>
        <title>Email - Tennis4All</title>
    </head>
    <body style="text-align: center;">

        <table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 7px; background-color: #fff; color: #495057; margin: 0; box-shadow: 0 0.75rem 1.5rem rgba(18,38,63,.03); border: 1px solid #eee; text-align: center;" bgcolor="#fff">
            <tbody>

                <a href="{{ url('/') }}" title="logo" target="_blank">
                    <img width="120" style="margin: 0 auto;" src="{{ asset('/assets/uploads/default/logo.png') }}" title="logo" alt="logo">
                </a>
                
                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="alert alert-warning" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; color: #222; font-weight: 400; text-align: justify; border-radius: 7px 7px 0 0; background-color: #edf2f7; margin: 0; padding: 10px;" align="center" bgcolor="#71b6f9" valign="top">
                        <span style="margin-left: 10px; color: #34c38f !important;"><b style="font-weight: 500;">Hello {{ $user['name'] }}, Congratulations!</b> Your full membership request is approved.</span>
                    </td>
                </tr>

                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 18px; vertical-align: top; margin: 0; padding: 15px 20px 10px; color: #222; font-weight: 500;"valign="top">
                        Tennis4All Administration
                    </td>
                </tr>
                
                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0px 20px 10px; text-align: justify;" valign="top">
                        We are glad to inform you that your request to be a full member is accepted by us. You have full membership now. Please <a href="{{ url('/login') }}">login</a> here with your credentials to explore your membership status and tools.
                    </td>
                </tr>

                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="content-block" style="text-align: center;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0; font-weight: 500; padding-bottom: 15px;" valign="top">
                        © 2023 {{ config('app.name') }}
                    </td>
                </tr>
            </tbody>
        </table>

    </body>
</html>