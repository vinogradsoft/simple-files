<?php
declare(strict_types=1);

namespace Test\Unit\Directory\WriteTo;

use Test\Cases\FileSystemCase;
use Test\Cases\IoEnvCase;
use Vinograd\SimpleFiles\Directory;
use Vinograd\SimpleFiles\File;
use Vinograd\SimpleFiles\FileFunctionalitiesContext;

class WriteToNotBindedWithSetLocalNameTest extends IoEnvCase
{
    protected $outDirectoryName = 'WriteToNotBindedWithSetLocalNameTest';

    public function testWriteTo()
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

        $file1->setContent('file1.txt');
        $file2->setContent('file2.txt');
        $file3->setContent('file3.txt');
        $file4->setContent('file4.txt');
        $file5->setContent('file5.txt');
        $file6->setContent('file6.txt');
        $file7->setContent('file7.txt');
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
        $childL->setLocalName('renamedChild');
        $file3->setLocalName('file3renamed.txt');
        $childL->writeTo($outPath);

        self::assertDirectoryExists($outPath . '/renamedChild/root/child1/child2/child3/child4/child5');
        self::assertFileExists($file1Path = $outPath . '/renamedChild/file1.txt');
        self::assertFileExists($file7Path = $outPath . '/renamedChild/root/file7.txt');
        self::assertFileExists($file6Path = $outPath . '/renamedChild/root/child1/file6.txt');
        self::assertFileExists($file5Path = $outPath . '/renamedChild/root/child1/child2/file5.txt');
        self::assertFileExists($file4Path = $outPath . '/renamedChild/root/child1/child2/child3/file4.txt');
        self::assertFileExists($file3Path = $outPath . '/renamedChild/root/child1/child2/child3/child4/file3renamed.txt');
        self::assertFileExists($file2Path = $outPath . '/renamedChild/root/child1/child2/child3/child4/child5/file2.txt');

        self::assertEquals('file1.txt', file_get_contents($file1Path));
        self::assertEquals('file2.txt', file_get_contents($file2Path));
        self::assertEquals('file3.txt', file_get_contents($file3Path));
        self::assertEquals('file4.txt', file_get_contents($file4Path));
        self::assertEquals('file5.txt', file_get_contents($file5Path));
        self::assertEquals('file6.txt', file_get_contents($file6Path));
        self::assertEquals('file7.txt', file_get_contents($file7Path));
    }

}
