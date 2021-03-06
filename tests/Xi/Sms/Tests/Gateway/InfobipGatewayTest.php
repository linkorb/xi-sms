<?php

namespace Xi\Sms\Tests\Gateway;

use Xi\Sms\Gateway\InfobipGateway;

class InfobipGatewayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function sendsCorrectlyFormattedXmlToRightPlace()
    {
        $ed = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $gateway = new InfobipGateway($ed, 'lussuta', 'tussia', 'http://dr-kobros.com/api');

        $browser = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock();

        $gateway->setClient($browser);

        $xml =
            "XML=<SMS><authentication><username>lussuta</username><password>tussia</password>" .
            "</authentication><message>" .
            "<sender>Losoposki</sender><datacoding>3</datacoding><text>Tehdaan sovinto, pojat.</text></message>" .
            "<recipients><gsm>358503028030</gsm><gsm>358407682810</gsm></recipients></SMS>\n";

        $browser
            ->expects($this->once())->method('post')
            ->with(
                'http://dr-kobros.com/api/v3/sendsms/xml',
                array(),
                $xml
            );

        $message = new \Xi\Sms\SmsMessage(
            'Tehdaan sovinto, pojat.',
            'Losoposki',
            '358503028030'
        );

        $message->addTo('358407682810');

        $ed
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                'xi_sms.send',
                $this->isInstanceOf('Xi\Sms\Event\SmsMessageEvent')
            );

        $ret = $gateway->send($message);
        $this->assertTrue($ret);
    }
}
