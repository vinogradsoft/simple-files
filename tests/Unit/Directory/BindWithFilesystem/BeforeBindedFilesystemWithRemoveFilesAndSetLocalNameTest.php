<?php

namespace Test\Unit\Directory\BindWithFilesystem;

use Test\Cases\FileSystemCase;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class BeforeBindedFilesystemWithRemoveFilesAndSetLocalNameTest extends FileSystemCase
{
    private $outPath;

    public function setUp(): void
    {
        $this->createDirectory($this->outPath = $this->getRuntimePath() . '/BeforeBindedFilesystemWithRemoveFilesAndSetLocalNameTest');
    }

    public function testBindWithFilesystem()
    {
        $outPath = $this->outPath;

        $root = new Directory('root');
        $child1 = new Directory('child1');
        $child2 = new Directory('child2');
        $child3 = new Directory('child3');
        $child4 = new Directory('child4');
        $child5 = new Directory('child5');
        $childL = new Directory('childL');

        $file1 = new File('file1.txt');
        $file2 = new File('file2.txt');
        $file3 = new File('file3.txt');
        $file4 = new File('file4.txt');
        $file5 = new File('file5.txt');
        $file6 = new File('file6.txt');
        $file7 = new File('file7.txt');

        //fresh mix root #1
        $root->addDirectory($child1)->addDirectory($child2)->addDirectory($child3);

        /**
         * result fresh mix root #1
         * root
         * |___child1
         *     |___child2
         *         |___child3
         */

        //fresh mix root #2
        $root->addDirectory($child4)->addDirectory($child5)->addDirectory($childL);

        /**
         * result fresh mix root #2
         * root
         * |___child1
         * |   |___child2
         * |       |___child3
         * |
         * |___child4
         *     |___child5
         *        |___childL
         */

        //fresh mix files #1
        $root->addFile($file1);
        $child1->addFile($file2);
        $child2->addFile($file3);
        $child3->addFile($file4);
        $child4->addFile($file5);
        $child5->addFile($file6);
        $childL->addFile($file7);
        /**
         * result fresh mix files #1
         * root
         * |___child1
         * |   |___child2
         * |   |   |___child3
         * |   |   |   |___file4.txt
         * |   |   |___file3.txt
         * |   |___file2.txt
         * |
         * |___child4
         * |   |___child5
         * |   |   |___childL
         * |   |   |   |____file7.txt
         * |   |   |____file6.txt
         * |   |___file5.txt
         * |
         * |___file1.txt
         */

        //fresh mix files #2
        $root->addFile($file7);
        $child1->addFile($file6);
        $child2->addFile($file5);
        // $child3->addFile($file4); //Already Exist
        $child4->addFile($file3);
        $child5->addFile($file2);
        $childL->addFile($file1);

        /**
         * result fresh mix files #2
         * root
         * |___child1
         * |   |___child2
         * |   |   |___child3
         * |   |   |   |___file4.txt
         * |   |   |___file5.txt
         * |   |___file6.txt
         * |
         * |___child4
         * |   |___child5
         * |   |   |___childL
         * |   |   |   |____file1.txt
         * |   |   |____file2.txt
         * |   |___file3.txt
         * |___file7.txt
         */

        //fresh mix directory #1
        $childL->addDirectory($root);
        /**
         * result fresh mix directory #1
         * childL
         * |___root
         * |   |___child1
         * |   |   |___child2
         * |   |   |   |___child3
         * |   |   |   |   |___file4.txt
         * |   |   |   |___file5.txt
         * |   |   |___file6.txt
         * |   |
         * |   |___child4
         * |   |   |___child5
         * |   |   |   |___file2.txt
         * |   |   |___file3.txt
         * |   |___file7.txt
         * |
         * |___file1.txt
         */

        //fresh mix directory #2
        $child3->addDirectory($child4);
        /**
         * result fresh mix directory #2
         * childL
         * |___root
         * |   |___child1
         * |   |   |___child2
         * |   |   |   |___child3
         * |   |   |   |   |___child4
         * |   |   |   |   |   |___child5
         * |   |   |   |   |   |   |___file2.txt
         * |   |   |   |   |   |___file3.txt
         * |   |   |   |   |___file4.txt
         * |   |   |   |___file5.txt
         * |   |   |___file6.txt
         * |   |___file7.txt
         * |___file1.txt
         */

        //fresh mix files remove all
        $root->removeFile($file7);
        $child1->removeFile($file6);
        $child2->removeFile($file5);
        $child3->removeFile($file4);
        $child4->removeFile($file3);
        $child5->removeFile($file2);
        $childL->removeFile($file1);
        /**
         * result fresh mix files remove all
         * childL
         * |___root
         *     |___child1
         *         |___child2
         *             |___child3
         *                 |___child4
         *                     |___child5
         */
        $childL->setLocalName('renamedChild');
        $childL->bindWithFilesystem($outPath);

        self::assertDirectoryExists($outPath . '/renamedChild/root/child1/child2/child3/child4/child5');
        self::assertFileNotExists($outPath . '/renamedChild/file1.txt');
        self::assertFileNotExists($outPath . '/renamedChild/root/file7.txt');
        self::assertFileNotExists($outPath . '/renamedChild/root/child1/file6.txt');
        self::assertFileNotExists($outPath . '/renamedChild/root/child1/child2/file5.txt');
        self::assertFileNotExists($outPath . '/renamedChild/root/child1/child2/child3/file4.txt');
        self::assertFileNotExists($outPath . '/renamedChild/root/child1/child2/child3/child4/file3.txt');
        self::assertFileNotExists($outPath . '/renamedChild/root/child1/child2/child3/child4/child5/file2.txt');

        self::assertFileNotExists($outPath . '/childL/file1.txt');
        self::assertFileNotExists($outPath . '/childL/root/file7.txt');
        self::assertFileNotExists($outPath . '/childL/root/child1/file6.txt');
        self::assertFileNotExists($outPath . '/childL/root/child1/child2/file5.txt');
        self::assertFileNotExists($outPath . '/childL/root/child1/child2/child3/file4.txt');
        self::assertFileNotExists($outPath . '/childL/root/child1/child2/child3/child4/file3.txt');
        self::assertFileNotExists($outPath . '/childL/root/child1/child2/child3/child4/child5/file2.txt');
    }

    public function tearDown(): void
    {
        FileFunctionalitiesContext::reset();
        $this->delete($this->outPath);
    }
}
