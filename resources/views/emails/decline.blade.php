<!DOCTYPE html>
<html>
    <head>
        <title>Email - Tennis4All</title>
    </head>
    <body>

        <table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 7px; background-color: #fff; color: #495057; margin: 0; box-shadow: 0 0.75rem 1.5rem rgba(18,38,63,.03); border: 1px solid #eee; text-align: center;" bgcolor="#fff">
            <tbody>

                <a href="{{ url('/') }}" title="logo" target="_blank">
                    <img width="120" style="margin: 0 auto;" src="{{ asset('/assets/uploads/default/logo.png') }}" title="logo" alt="logo">
                </a>
                
                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="alert alert-warning" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; color: #222; font-weight: 400; text-align: justify; border-radius: 7px 7px 0 0; background-color: #edf2f7; margin: 0; padding: 10px;" align="center" bgcolor="#71b6f9" valign="top">
                        
                        <span style="margin-left: 10px; color: #f46a6a !important;"><b style="font-weight: 500;">Hello {{ $user['name'] }}, Disapproval!</b> Your account is declined.</span>
                        
                    </td>
                </tr>

                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 18px; vertical-align: top; margin: 0; padding: 15px 20px 10px; color: #222; font-weight: 500;" valign="top">
                        Tennis4All Administration
                    </td>
                </tr>
                
                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0px 20px 10px;" valign="top">
                            We are sorry to inform you that your request to join is declined. Please try again later with valid information or <a href="{{ url('/login') }}">contact us</a> for any kind of help.
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