<?php


class MegaCrypterTest extends PHPUnit_Framework_TestCase
{
    public function testGetOptionalFlags()
    {
        $expected_flags = ['EXTRAINFO', 'HIDENAME', 'PASSWORD', 'EXPIRE', 'NOEXPIRETOKEN', 'REFERER', 'EMAIL', 'ZOMBIE', 'AUTH'];

        $optional_flags = $this->_getOptionalFlags();

        $this->assertInternalType('array', $optional_flags);

        $this->assertEquals($expected_flags, array_keys($optional_flags));

    }

    public function testPack()
    {
        $expected_packs = [

            'EXTRAINFO' => ['in' => ['hola'], 'out' => pack('n', strlen('hola')-1).'hola'],

            'PASSWORD' => ['in' => ['mypassword', 'mysalt'], 'out' => pack('C', Utils_MegaCrypter::PBKDF2_ITERATIONS_LOG2 - 1) . hash_pbkdf2('sha256', 'mypassword', 'mysalt', pow(2, Utils_MegaCrypter::PBKDF2_ITERATIONS_LOG2), 0, true) . 'mysalt'],

            'EXPIRE' => ['in' => [1452961699], 'out' => pack('NN', (1452961699 >> 32) & 0xFFFFFFFF, 1452961699 & 0xFFFFFFFF)],

            'REFERER' => ['in' => ['www.foo.com'], 'out' => pack('n', strlen('www.foo.com')-1) . 'www.foo.com'],

            'EMAIL' => ['in' => ['foo@foo.com'], 'out' => pack('C', strlen('foo@foo.com')-1) . 'foo@foo.com'],

            'ZOMBIE' => ['in' => ['127.0.0.1'], 'out' => pack('CCCC', 127,0,0,1)],

            'AUTH' => ['in' => ['auth_data'], 'out' => pack('n', strlen('auth_data')-1).'auth_data']

        ];

        $optional_flags = $this->_getOptionalFlags();

        foreach($optional_flags as $flag => $val) {

            if(array_key_exists($flag, $expected_packs)) {

                $this->assertEquals($expected_packs[$flag]['out'], call_user_func_array($val['pack'], $expected_packs[$flag]['in']));
            }
        }
    }

    public function testUnpack()
    {
        $expected_unpacks = [

            'EXTRAINFO' => ['in' => [pack('n', strlen('hola')-1).'hola'], 'out' => 'hola'],

            'PASSWORD' => ['in' => [($password_pack = pack('C', Utils_MegaCrypter::PBKDF2_ITERATIONS_LOG2 - 1) . ($hash_pbkdf2=hash_pbkdf2('sha256', 'mypassword', md5('mysalt', true), pow(2, Utils_MegaCrypter::PBKDF2_ITERATIONS_LOG2), 0, true)) . md5('mysalt', true))], 'out' => ['iterations' => Utils_MegaCrypter::PBKDF2_ITERATIONS_LOG2, 'pbkdf2_hash' => $hash_pbkdf2, 'salt' => md5('mysalt', true) ]],

            'EXPIRE' => ['in' => [pack('NN', (1452961699 >> 32) & 0xFFFFFFFF, 1452961699 & 0xFFFFFFFF)], 'out' => 1452961699],

            'REFERER' => ['in' => [pack('n', strlen('www.foo.com')-1) . 'www.foo.com'], 'out' => 'www.foo.com'],

            'EMAIL' => ['in' => [pack('C', strlen('foo@foo.com')-1) . 'foo@foo.com'], 'out' => 'foo@foo.com'],

            'ZOMBIE' => ['in' => [pack('CCCC', 127,0,0,1)],'out' => '127.0.0.1'],

            'AUTH' => ['in' => [pack('n', strlen('auth_data')-1).'auth_data'], 'out' => 'auth_data']

        ];

        $optional_flags = $this->_getOptionalFlags();

        $offset = 0;

        foreach($optional_flags as $flag => $val) {

            if(array_key_exists($flag, $expected_unpacks)) {

                $this->assertEquals($expected_unpacks[$flag]['out'], call_user_func_array($val['unpack'], array_merge($expected_unpacks[$flag]['in'], [&$offset])));
                $offset = 0;
            }
        }
    }

    public function testEncryptDecryptLink()
    {
        $method = new ReflectionMethod(
            'Utils_MegaCrypter', '_encryptLink'
        );

        $method->setAccessible(TRUE);

        $link = 'https://mega.nz/#!RF1GiAzT!JznAr3lWn-A28Sp6CqmqnrEJymNtkgkESSwfunSRJf4';

        $clink = $method->invoke(new Utils_MegaCrypter, $link,
            [
                'EXTRAINFO' => 'hola',
                'HIDENAME' => true,
                'PASSWORD' => 'mypassword',
                'EXPIRE' => 2452961699,
                'NOEXPIRETOKEN' => true,
                'REFERER' => 'www.foo.com',
                'EMAIL' => 'foo@foo.com',
                'ZOMBIE' => '127.0.0.1',
                'AUTH' => 'authdummydata'

            ]);

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $dlink = Utils_MegaCrypter::decryptLink($clink['link']);

        $this->assertEquals('RF1GiAzT', $dlink['file_id']);
        $this->assertEquals('JznAr3lWn-A28Sp6CqmqnrEJymNtkgkESSwfunSRJf4', $dlink['file_key']);
        $this->assertEquals('hola', $dlink['extra_info']);
        $this->assertEquals(true, $dlink['hide_name']);
        $this->assertInternalType('array', $dlink['pass']);
        $this->assertEquals(2452961699, $dlink['expire']);
        $this->assertEquals($clink['secret'], base64_decode($dlink['no_expire_token']));
        $this->assertEquals('www.foo.com', $dlink['referer']);
        $this->assertEquals('foo@foo.com', $dlink['email']);
        $this->assertEquals('127.0.0.1', $dlink['zombie']);
        $this->assertEquals('authdummydata', $dlink['auth']);
    }

    private function _getOptionalFlags()
    {
        $method = new ReflectionMethod(
            'Utils_MegaCrypter', '_getOptionalFlags'
        );

        $method->setAccessible(TRUE);

        $optional_flags = $method->invoke(new Utils_MegaCrypter);

        return $optional_flags;
    }
}
