<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background-color:#faf9f5;font-family:Helvetica,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 0;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#d97757;padding:24px 32px;">
                            <p style="margin:0;font-size:18px;font-weight:700;color:#ffffff;">New Invoice</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 20px;font-size:15px;color:#24211c;">Hi {{ $clientName }},</p>
                            <p style="margin:0 0 24px;font-size:15px;color:#24211c;line-height:1.6;">
                                {{ $businessName }} has sent you invoice <strong>{{ $invoiceNumber }}</strong>{{ $dueDate ? ', due on '.\Carbon\Carbon::parse($dueDate)->format('M j, Y') : '' }}. The invoice is attached as a PDF.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fbe8df;border-radius:12px;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;text-align:center;">
                                        <p style="margin:0 0 6px;font-size:11px;font-weight:600;color:#7a7368;letter-spacing:0.06em;text-transform:uppercase;">Amount Due</p>
                                        <p style="margin:0;font-size:22px;font-weight:700;color:#d97757;">₹{{ number_format((float) $total, 2) }}</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0;font-size:13px;color:#7a7368;line-height:1.6;">
                                Please see the attached PDF for full details.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
