<html>
<head>
    <meta content="text/html;charset=UTF-8" http-equiv="Content-Type">
    {if $subject}<title>{$subject}</title>{/if}
{literal}
    <style type="text/css">
        @media only screen and (max-width: 480px) {
            table.contenttable { width:320px !important; }
        }
    </style>
{/literal}
</head>
<body style="margin:0;padding:0;" >
<table width="640" cellspacing="0" cellpadding="10" align="center" class="contenttable">
    <tr>
        <td align="left" valign="top">
            {$htmlmessage}
        </td>
    </tr>
</table>
<body>
</html>
