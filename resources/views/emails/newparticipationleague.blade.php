<!DOCTYPE html>
<html>
    <head>
        <title>Email - Tennis4All</title>
    </head>
    <body style="text-align: center !important;">

        <table class="main" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 7px; background-color: #fff; color: #495057; margin: 0; box-shadow: 0 0.75rem 1.5rem rgba(18,38,63,.03); border: 1px solid #eee;" bgcolor="#fff">
            <tbody>
                <a href="{{ url('/') }}" title="logo" target="_blank">
                    <img width="120" style="margin: 0 auto;" src="{{ asset('/assets/uploads/default/logo.png') }}" title="logo" alt="logo">
                </a>

                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="alert alert-warning" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; color: #222; font-weight: 400; text-align: justify; border-radius: 7px 7px 0 0; background-color: #edf2f7; margin: 0; padding: 10px;" align="center" bgcolor="#71b6f9" valign="top">
                        <span style="margin-left: 10px;">{{ $user['name'] }} wants to participate in the {{ $payment['draw_status'] }} of the league.</span>
                    </td>
                </tr>

                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 18px; vertical-align: top; margin: 0; padding: 15px 20px 10px; color: #222; font-weight: 500;" valign="top">
                        Tennis4All
                    </td>
                </tr>
                
                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0px 20px 10px;" valign="top">
                        1 new payment request is sent just now by a player. To accept or decline, please <a href="{{ url('/login') }}">login</a>
                    </td>
                </tr>

                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td class="content-wrap" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 20px;" valign="top">

                        <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                            <tbody>
                                

                                
                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td width="10%" align="left" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; color: #908888;" valign="top">
                                        Name:
                                    </td>

                                    <td width="90%" align="right" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: justify;" valign="top">
                                        {{ $user['name'] }}
                                    </td>
                                </tr>

                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td width="10%" align="left" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; color: #908888;" valign="top">
                                        E-mail:
                                    </td>

                                    <td width="90%" align="right" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: justify;" valign="top">
                                        {{ $user['email'] }}
                                    </td>
                                </tr>

                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td width="10%" align="left" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; color: #908888;" valign="top">
                                        Phone:
                                    </td>

                                    <td width="90%" align="right" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: justify;" valign="top">
                                        {{ $user['phone'] }}
                                    </td>
                                </tr>

                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td width="10%" align="left" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; color: #908888;" valign="top">
                                        Code:
                                    </td>

                                    <td width="90%" align="right" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: justify;" valign="top">
                                        {{ $payment['participation_code'] }}
                                    </td>
                                </tr>

                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td width="10%" align="left" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; color: #908888;" valign="top">
                                        League:
                                    </td>

                                    <td width="90%" align="right" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: justify;" valign="top">
                                        @foreach(json_decode($payment['leagues']) as $league)
                                            <?php $league = \App\Models\League::findOrFail($league); ?>
                                            {{ $league->name }}. 
                                        @endforeach
                                    </td>
                                </tr>

                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td width="10%" align="left" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; color: #908888;" valign="top">
                                        Lea. Fees:
                                    </td>

                                    <td width="90%" align="right" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: justify;" valign="top">
                                        <span style="font-weight: 500;"> {{ $payment->league_fees }} €</span>
                                    </td>
                                </tr>

                                <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <td width="10%" align="left" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; color: #908888;" valign="top">
                                        Type:
                                    </td>

                                    <td width="90%" align="right" class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px; text-align: justify;" valign="top">
                                        {{ $payment['league_draw_status'] }}
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
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