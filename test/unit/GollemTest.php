<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Gollem::class)]
class GollemTest extends TestCase
{
    #[DataProvider('stripAPIPathProvider')]
    public function testStripAPIPath(string $input, string $expected): void
    {
        $this->assertSame($expected, Gollem::stripAPIPath($input));
    }

    public static function stripAPIPathProvider(): array
    {
        return [
            'empty string' => ['', ''],
            'just gollem' => ['gollem', ''],
            'leading slash gollem' => ['/gollem', ''],
            'gollem with subpath' => ['gollem/mybackend/dir/file.txt', 'mybackend/dir/file.txt'],
            'leading slash with subpath' => ['/gollem/mybackend/dir', 'mybackend/dir'],
            'trailing slash' => ['/gollem/mybackend/', 'mybackend'],
        ];
    }

    #[DataProvider('pathEncodeProvider')]
    public function testPathEncode(string $input, string $expected): void
    {
        $this->assertSame($expected, Gollem::pathEncode($input));
    }

    public static function pathEncodeProvider(): array
    {
        return [
            'simple path' => ['/dir/file.txt', '/dir/file.txt'],
            'spaces' => ['/my dir/my file.txt', '/my%20dir/my%20file.txt'],
            'plus sign' => ['/dir/file+name.txt', '/dir/file%2Bname.txt'],
            'special chars' => ['/dir/file name (1).txt', '/dir/file%20name%20%281%29.txt'],
        ];
    }

    #[DataProvider('getVFSPathProvider')]
    public function testGetVFSPath(string $fullpath, array $expected): void
    {
        $this->assertSame($expected, Gollem::getVFSPath($fullpath));
    }

    public static function getVFSPathProvider(): array
    {
        return [
            'file in dir' => ['/home/user/file.txt', ['file.txt', '/home/user']],
            'root file' => ['/file.txt', ['file.txt', '']],
            'no slash' => ['file.txt', ['file.txt', '']],
            'nested' => ['/a/b/c/d.txt', ['d.txt', '/a/b/c']],
            'directory path' => ['/a/b/c', ['c', '/a/b']],
        ];
    }

    #[DataProvider('formatFileSizeProvider')]
    public function testFormatFileSize(int $size, string $expected): void
    {
        $this->assertSame($expected, Gollem::formatFileSize($size));
    }

    public static function formatFileSizeProvider(): array
    {
        return [
            'zero bytes' => [0, '0 B'],
            'bytes' => [512, '512 B'],
            'one kB' => [1024, '1 kB'],
            'kB range' => [1536, '1.5 kB'],
            'one MB' => [1048576, '1 MB'],
            'one GB' => [1073741824, '1 GB'],
        ];
    }

    #[DataProvider('subdirectoryProvider')]
    public function testSubdirectory(string $base, string $dir, string $expected): void
    {
        $this->assertSame($expected, Gollem::subdirectory($base, $dir));
    }

    public static function subdirectoryProvider(): array
    {
        return [
            'empty base' => ['', 'subdir', 'subdir'],
            'base with trailing slash' => ['/home/', 'subdir', '/home/subdir'],
            'base without trailing slash' => ['/home', 'subdir', '/home/subdir'],
        ];
    }

    #[DataProvider('realUncPathProvider')]
    public function testRealUncPath(string $input, string $expected): void
    {
        $this->assertSame($expected, Gollem::realUncPath($input));
    }

    public static function realUncPathProvider(): array
    {
        return [
            'simple path' => ['/home/user', '/home/user'],
            'dot segments' => ['/home/user/../other', '/home/other'],
            'unc path' => ['//server/share/dir', '//server/share/dir'],
            'unc with dots' => ['//server/share/a/../b', '//server/share/b'],
        ];
    }
}
