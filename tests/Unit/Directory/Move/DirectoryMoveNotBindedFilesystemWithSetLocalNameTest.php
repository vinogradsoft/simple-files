<?php
declare(strict_types=1);

namespace Test\Unit\Directory\Move;

use Test\Cases\IoEnvCase;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;

class DirectoryMoveNotBindedFilesystemWithSetLocalNameTest extends IoEnvCase
{
    protected $outDirectoryName = 'DirectoryMoveNotBindedFilesystemWithSetLocalNameTest';

    public function testMove()
    {
        $outPath = $this->outPath;
        $this->createDirectory($outPath . '/move');

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
        $childL->setLocalName('renamedChild');
        $file3->setLocalName('file3renamed.txt');
        $childL->move($outPath . '/move');

        self::assertDirectoryExists($outPath . '/move/renamedChild/root/child1/child2/child3');
        self::assertDirectoryExists($outPath . '/move/renamedChild/root/child4/child5');
        self::assertFileExists($outPath . '/move/renamedChild/file1.txt');
        self::assertFileExists($outPath . '/move/renamedChild/root/file7.txt');
        self::assertFileExists($outPath . '/move/renamedChild/root/child1/file6.txt');
        self::assertFileExists($outPath . '/move/renamedChild/root/child1/child2/file5.txt');
        self::assertFileExists($outPath . '/move/renamedChild/root/child1/child2/child3/file4.txt');
        self::assertFileExists($outPath . '/move/renamedChild/root/child4/file3renamed.txt');
        self::assertFileExists($outPath . '/move/renamedChild/root/child4/child5/file2.txt');

        self::assertEquals($outPath . '/move/renamedChild/file1.txt', $file1->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/file7.txt', $file7->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child1/file6.txt', $file6->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child1/child2/file5.txt', $file5->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child1/child2/child3/file4.txt', $file4->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child4/file3renamed.txt', $file3->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child4/child5/file2.txt', $file2->getPath()->getSource());

        self::assertEquals($outPath . '/move/renamedChild', $childL->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root', $root->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child1', $child1->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child1/child2', $child2->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child1/child2/child3', $child3->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child4', $child4->getPath()->getSource());
        self::assertEquals($outPath . '/move/renamedChild/root/child4/child5', $child5->getPath()->getSource());

        self::assertTrue($file1->isBinded());
        self::assertTrue($file2->isBinded());
        self::assertTrue($file3->isBinded());
        self::assertTrue($file4->isBinded());
        self::assertTrue($file5->isBinded());
        self::assertTrue($file6->isBinded());
        self::assertTrue($file7->isBinded());

        self::assertTrue($childL->isBinded());
        self::assertTrue($root->isBinded());
        self::assertTrue($child1->isBinded());
        self::assertTrue($child2->isBinded());
        self::assertTrue($child3->isBinded());
        self::assertTrue($child4->isBinded());
        self::assertTrue($child5->isBinded());
    }

}
