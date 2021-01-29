<?php

namespace WHMCS\Module\Addon\Dondominio\Services;

use WHMCS\Module\Addon\Dondominio\Services\Contracts\EmailService_Interface;

class Email_Service extends AbstractService implements EmailService_Interface
{
    /**
     * Sends an Email about new tlds available
     * 
     * @return void
     */
    public function sendNewTldsEmail(array $tlds)
    {
        $formatTlds = array_map(function($tld) {
            return is_object($tld) ? $tld->toArray() : $tld;
        }, $tlds);

        $template = $this->getNewTldsTemplate($formatTlds);
        $this->sendEmail("New TLDs available", $template);
    }

    /**
     * Sends an Email about updated TLDs prices
     * 
     * @return void
     */
    public function sendUpdatedTldsEmail(array $tlds)
    {
        $formatTlds = array_map(function($tld) {
            return is_object($tld) ? $tld->toArray() : $tld;
        }, $tlds);

        // Check and format about differences

        foreach ($formatTlds as &$formatTld) {
            $register_difference = $formatTld['old_register_price'] - $formatTld['register_price'];

            if ($formatTld['register_price'] != $formatTld['old_register_price']) {                
                if ($formatTld['old_register_price'] < $formatTld['register_price']) {
                    $register_difference = '+ ' . number_format(($register_difference * -1), 2, '.', ',');
                } else {
                    $register_difference = '- ' . number_format($register_difference, 2, '.', ',');
                }
            }

            $transfer_difference = $formatTld['old_transfer_price'] - $formatTld['transfer_price'];

            if ($formatTld['transfer_price'] != $formatTld['old_transfer_price']) {                
                if ($formatTld['old_transfer_price'] < $formatTld['transfer_price']) {
                    $transfer_difference = '+ ' . number_format(($transfer_difference * -1), 2, '.', ',');
                } else {
                    $transfer_difference = '- ' . number_format($transfer_difference, 2, '.', ',');
                }
            }

            $renew_difference = $formatTld['old_renew_price'] - $formatTld['renew_price'];

            if ($formatTld['renew_price'] != $formatTld['old_renew_price']) {                
                if ($formatTld['old_renew_price'] < $formatTld['renew_price']) {
                    $renew_difference = '+ ' . number_format(($renew_difference * -1), 2, '.', ',');
                } else {
                    $renew_difference = '- ' . number_format($renew_difference, 2, '.', ',');
                }
            }

            $formatTld['register_difference'] = $register_difference;
            $formatTld['transfer_difference'] = $transfer_difference;
            $formatTld['renew_difference'] = $renew_difference;
        }

        // Send Email

        $template = $this->getUpdatedTldsTemplate($formatTlds);
        $this->sendEmail("TLD prices updated", $template);
    }

    /**
     * Sends an Email
     * 
     * @param string $subject Subject of Email
     * @param string $body Body of Email
     * 
     * @return void
     */
    public function sendEmail($subject, $body)
    {
        $notifications = $this->getApp()->getService('settings')->getSetting('notifications_enabled');
        $notifications_email = $this->getApp()->getService('settings')->getSetting('notifications_email');

        if ($notifications != '1' || empty($notifications_email)) {
            return;
        }

        $headers  = "From: DonDominio WHMCS Addon <no-reply@" . php_uname( 'n' ) . ">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html;r charset=iso-8859-1\r\n";

        $succesMail = mail($notifications_email, $subject, $body, $headers);

        if (!$succesMail) {
            logActivity(sprintf("Couldn't send email to %s with subject '%s'", $notifications_email, $subject));
        }
    }

    /**
     * Gets HTML Template for new tlds email
     * 
     * @param array $tlds tlds in an specific format
     * 
     * @return string Template in HTML format
     */
    public function getNewTldsTemplate(array $tlds)
    {
        $html = "
            <!doctype>
            <html>
                <head>
                    <style>
                    <!--
                    TABLE {
                        border: 0px solid black;
                        border-width: 0px 0px 1px 0px;
                    }

                    TD {
                        border: 1px solid black;
                        border-width: 1px 0px 0px 1px;
                        text-align: center;
                    }

                    TH {
                        border: 1px solid black;
                        border-width: 1px 0px 0px 1px;
                        text-align: center;
                        font-weight: bold;
                        background-color: #d0d0d0;
                    }

                    .right {
                        border-width: 1px 1px 0px 1px;
                    }
                    -->
                    </style>
                </head>
                <body>
                    <h2>New TLDs available</h2>

                    <p>The following TLDs have been added to DonDominio and are now available to register:</p>
                    <p>
                    
                    <table border='1' width='100%' cellspacing='0' cellpadding='5'>
                    <thead>
                        <tr>
                            <th>TLD</th>
                            <th>Registration</th>
                            <th>Transfer</th>
                            <th class='right'>Renewal</th>
                        </tr>
                    </thead>
                    <tbody>
        ";
        
        foreach ($tlds as $data) {
            $html .= "
                        <tr>
                            <td>" . $data['tld'] . "</td>
                            <td>" . $data['register_price'] . "&nbsp;</td>
                            <td>" . $data['transfer_price'] . "&nbsp;</td>
                            <td class='right'>" . $data['renew_price'] . "&nbsp;</td>
                        </tr>
            ";
        }

        $html .= "
                    </tbody>
                    </table>
                </body>
            </html>
        ";

        return $html;
    }

    /**
     * Gets HTML Template for updated tlds email
     * 
     * @param array $tlds tlds in an specific format
     * 
     * @return string Template in HTML format
     */
    public function getUpdatedTldsTemplate(array $tlds)
    {
        $html = "
            <!doctype>
            <html>
                <head>
                    <style>
                    <!--
                    TABLE {
                        border: 0px solid black;
                        border-width: 0px 0px 2px 0px;
                    }
                    
                    TD {
                        border: 1px solid black;
                        border-width: 1px 1px 1px 0px;
                        text-align: center;
                    }
                    
                    TH {
                        border: 1px solid black;
                        border-width: 4px 1px 2px 0px;
                        text-align: center;
                        font-weight: bold;
                        background-color: #d0d0d0;
                    }

                    .th-highlight {
                        background-color: #c0c0c0;
                        border-width: 4px 2px 2px 1px;
                    }
                    
                    .th-left {
                        border-width: 4px 1px 2px 4px;
                    }
                    
                    .th-right {
                        border-width: 4px 4px 2px 0px;
                    }
                    
                    .tld-name {
                        border-width: 2px 1px 2px 4px;
                        vertical-align: middle;
                    }

                    .tld-first-row {
                        border-width: 2px 1px 0px 0px;
                    }
                    
                    .tld-first-end {
                        border-width: 2px 4px 0px 0px;
                    }
                    
                    .tld-middle-end {
                        border-width: 1px 4px 1px 0px;
                    }
                    
                    .tld-last-row {
                        border-width: 0px 1px 2px 0px;
                    }
                    
                    .tld-last-end {
                        border-width: 0px 4px 2px 0px;
                    }

                    .tld-highlight {
                        background-color: #c0c0c0;
                        border-left: 1px solid #000;
                        border-right: 2px solid #000;
                    }
                    -->
                    </style>
                </head>
                <body>
                    <h2>TLD Prices Update Report</h2>
                    
                    <p>The following TLDs have changed prices recently:</p>
                    <p>
                    
                    <table border='1' width='100%' cellspacing='0' cellpadding='5'>
                    <thead>
                        <tr>
                            <th class='th-left'>TLD</th>
                            <th>Type</th>
                            <th>Old Price</th>
                            <th class='th-highlight'>New Price</th>
                            <th class='th-right'>Difference (+/-)</th>
                        </tr>
                    </thead>
                    <tbody>
        ";

        foreach ($tlds as $data) {
            $html .= "
                <tr>
                    <td rowspan='3' class='tld-name'><strong>" . $data['tld'] . "</strong></td>
                    <td class='tld-first-row'>Registration</td>
                    <td class='tld-first-row'>" . $data['old_register_price'] . "&nbsp;</td>
                    <td class='tld-first-row tld-highlight'>" . $data['register_price'] . "&nbsp;</td>
                    <td class='tld-first-end'>" . $data['register_difference'] . "&nbsp;</td>
                </tr>

                <tr>
                    <td class='tld-middle-row'>Transfer</td>
                    <td class='tld-middle-row'>" . $data['old_transfer_price'] . "&nbsp;</td>
                    <td class='tld-middle-row tld-highlight'>" . $data['transfer_price'] . "&nbsp;</td>
                    <td class='tld-middle-end'>" . $data['transfer_difference'] . "&nbsp;</td>
                </tr>

                <tr>
                    <td class='tld-last-row'>Renewal</td>
                    <td class='tld-last-row'>" . $data['old_renew_price'] . "&nbsp;</td>
                    <td class='tld-last-row tld-highlight'>" . $data['renew_price'] . "&nbsp;</td>
                    <td class='tld-last-end'>" . $data['renew_difference'] . "&nbsp;</td>
                </tr>
            ";
        }

        $html .= "
                    </tbody>
                    </table>
                </body>
            </html>
        ";

        return $html;
    }
}