<?php

namespace Test\Unit\AbstractDirectory;

use Test\Cases\CommonCase;
use Vinograd\IO\Exception\AlreadyExistException;
use Vinograd\IO\Exception\NotFoundException;
use Vinograd\SimpleFiles\AbstractDirectory;
use Vinograd\SimpleFiles\Exception\TreeException;

class AbstractDirectoryWorkDirectoriesTest extends CommonCase
{
    public function testAddDirectory()
    {
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $level1A = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);
        $level1B = $this->getMockForAbstractClass(AbstractDirectory::class, ['the/path/to/level1B']);

        $root->addDirectory($level1A);
        $root->addDirectory($level1B);
        self::assertSame($root, $level1A->getParent());
        self::assertSame($root, $level1B->getParent());
    }

    public function testAddDirectoryConsistency()
    {
        $root1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['root1']);
        $root2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/root']);
        $child = $this->getMockForAbstractClass(AbstractDirectory::class, ['the/path/to/child']);

        $root1->addDirectory($child);
        $root2->addDirectory($child);

        $directoriesRoot1 = $root1->getDirectories();
        $directoriesRoot2 = $root2->getDirectories();
        self::assertCount(0, $directoriesRoot1);
        self::assertCount(1, $directoriesRoot2);
        self::assertSame($directoriesRoot2[$child->getName()], $child);

        $root1->addDirectory($child);

        $directoriesRoot1 = $root1->getDirectories();
        $directoriesRoot2 = $root2->getDirectories();

        self::assertCount(1, $directoriesRoot1);
        self::assertCount(0, $directoriesRoot2);
        self::assertSame($directoriesRoot1[$child->getName()], $child);

        $root1->addDirectory($root2)->addDirectory($child);
        $child->addDirectory($root2);

        $directoriesRoot1 = $root1->getDirectories();
        self::assertCount(1, $directoriesRoot1);
        self::assertArrayHasKey('child', $directoriesRoot1);
        self::assertSame($root1, $child->getParent());
        $cDirectories = $child->getDirectories();
        self::assertArrayHasKey($root2->getLocalName(), $cDirectories);
        self::assertSame($root2->getParent(), $child);
    }

    public function testAddDirectoryConsistency2()
    {
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory']);

        /**
         * root
         * |___directory(1)
         *     |___directory(2)
         */
        $root->addDirectory($directory1)->addDirectory($directory2);

        /**
         * root
         * |___directory(2)
         *     |___directory(1)
         */
        $directory2->addDirectory($directory1);

        /**
         * directory(1)
         * |___root
         *     |___directory(2)
         */
        $directory1->addDirectory($root);


        $directoriesRoot = $root->getDirectories();
        $directories1 = $directory1->getDirectories();
        $directories2 = $directory2->getDirectories();

        self::assertCount(1, $directoriesRoot);
        self::assertArrayHasKey($directory2->getLocalName(), $directoriesRoot);
        self::assertSame($directory2, $directoriesRoot[$directory2->getLocalName()]);
        self::assertSame($directory1, $root->getParent());

        self::assertCount(1, $directories1);
        self::assertArrayHasKey($root->getLocalName(), $directories1);
        self::assertSame($root, $directories1[$root->getLocalName()]);
        self::assertEmpty($directory1->getParent());


        self::assertCount(0, $directories2);
        self::assertSame($root, $directory2->getParent());
    }

    public function testAddDirectoryConsistencyTwoTree()
    {
        $root1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $root1Directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory']);
        $root1Directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory']);

        $root2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $root2Directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory']);
        $root2Directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['directory']);

        //step 1
        /**
         * root(root1)
         * |___directory(root1.1)
         *     |___directory(root1.2)
         */
        $root1->addDirectory($root1Directory1)->addDirectory($root1Directory2);

        /**
         * root(root2)
         * |___directory(root2.1)
         *     |___directory(root2.2)
         */
        $root2->addDirectory($root2Directory1)->addDirectory($root2Directory2);

        //step 2
        /**
         * root(root1)
         * |___directory(root1.2)
         *     |___directory(root1.1)
         */
        $root1Directory2->addDirectory($root1Directory1);

        /**
         * root(root2)
         * |___directory(root2.2)
         *     |___directory(root2.1)
         */
        $root2Directory2->addDirectory($root2Directory1);

        //step 3

        /**
         * directory(root1.1)
         * |___root(root1)
         *     |___directory(root1.2)
         */
        $root1Directory1->addDirectory($root1);


        /**
         * directory(root2.1)
         * |___root(root2)
         *     |___directory(root2.2)
         */
        $root2Directory1->addDirectory($root2);

        //mix 1
        $root2Directory2->addDirectory($root1Directory2);

        /**
         * result root1
         * directory(root1.1)
         * |___root(root1)
         */
        $this->assertTreeItem($root1Directory1, 1, [$root1], null);
        $this->assertTreeItem($root1, 0, [], $root1Directory1);
        self::assertEquals('directory/root', $root1->getLocalPath());
        /**
         * result root2
         * directory(root2.1)
         * |___root(root2)
         *     |___directory(root2.2)
         *         |___directory(root1.2)
         */
        $this->assertTreeItem($root2Directory1, 1, [$root2], null);
        $this->assertTreeItem($root2, 1, [$root2Directory2], $root2Directory1);
        $this->assertTreeItem($root2Directory2, 1, [$root1Directory2], $root2);
        $this->assertTreeItem($root1Directory2, 0, [], $root2Directory2);
        self::assertEquals('directory/root/directory/directory', $root1Directory2->getLocalPath());

        // mix 2
        /**
         * root(root1)
         * |___directory(root1.1)
         */
        $root1->addDirectory($root1Directory1);

        $this->assertTreeItem($root1, 1, [$root1Directory1], null);
        $this->assertTreeItem($root1Directory1, 0, [], $root1);
        self::assertEquals('root/directory', $root1Directory1->getLocalPath());

        /**
         * directory(root2.1)
         * |___root(root2)
         *     |___directory(root2.2)
         *     |   |___directory(root1.2)
         *     |___root(root1)
         *         |___directory(root1.1)
         */
        $root2->addDirectory($root1);

        $this->assertTreeItem($root2Directory1, 1, [$root2], null);
        $this->assertTreeItem($root2, 2, [$root2Directory2, $root1], $root2Directory1);
        $this->assertTreeItem($root2Directory2, 1, [$root1Directory2], $root2);
        $this->assertTreeItem($root1Directory2, 0, [], $root2Directory2);
        $this->assertTreeItem($root1, 1, [$root1Directory1], $root2);
        $this->assertTreeItem($root1Directory1, 0, [], $root1);

        self::assertEquals('directory/root/directory/directory', $root1Directory2->getLocalPath());
        self::assertEquals('directory/root/root/directory', $root1Directory1->getLocalPath());

        /**
         * split
         * directory(root2.1)
         * |___root(root2)
         *     |___directory(root2.2)
         *     |   |___directory(root1.2)
         *     |___root(root1)
         * AND
         * directory(root1.1)
         */
        $root1Directory1->removeFromParent();
        $this->assertTreeItem($root1Directory1, 0, [], null);

        $this->assertTreeItem($root2Directory1, 1, [$root2], null);
        $this->assertTreeItem($root2, 2, [$root2Directory2, $root1], $root2Directory1);
        $this->assertTreeItem($root2Directory2, 1, [$root1Directory2], $root2);
        $this->assertTreeItem($root1Directory2, 0, [], $root2Directory2);
        $this->assertTreeItem($root1, 0, [], $root2);

        /**
         * directory(root2.1)
         * |___root(root2)
         *     |___root(root1)
         *
         * directory(root1.1)
         * |___directory(root2.2)
         *    |___directory(root1.2)
         */
        $root1Directory1->addDirectory($root2Directory2);
        $this->assertTreeItem($root2Directory1, 1, [$root2], null);
        $this->assertTreeItem($root2, 1, [$root1], $root2Directory1);
        $this->assertTreeItem($root1, 0, [], $root2);
        self::assertEquals('directory/root/root', $root1->getLocalPath());


        $this->assertTreeItem($root1Directory1, 1, [$root2Directory2], null);
        $this->assertTreeItem($root2Directory2, 1, [$root1Directory2], $root1Directory1);
        $this->assertTreeItem($root1Directory2, 0, [], $root2Directory2);
        self::assertEquals('directory/directory/directory', $root1Directory2->getLocalPath());
    }

    protected function assertTreeItem(
        AbstractDirectory  $directory,
        int                $countChildsDirs,
        array              $childsDirectories = [],
        ?AbstractDirectory $parent = null
    )
    {
        $directories = $directory->getDirectories();
        self::assertCount($countChildsDirs, $directories);
        if (empty($childsDirectories)) {
            self::assertEmpty($directories);
        } else {
            if ($countChildsDirs !== count($childsDirectories)) {
                self::fail();
            }
            foreach ($childsDirectories as $child) {
                self::assertSame($child, $directories[$child->getLocalName()]);
                self::assertArrayHasKey($child->getLocalName(), $directories);
            }
        }
        if (empty($parent)) {
            self::assertEmpty($directory->getParent());
        } else {
            self::assertSame($parent, $directory->getParent());
        }
    }

    public function testAddDirectoryAlreadyExist()
    {
        $this->expectException(AlreadyExistException::class);

        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $level1A = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);

        $root->addDirectory($level1A);
        $root->addDirectory($level1A);
    }

    public function testAddDirectorySwapPlaces()
    {
        $directory1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['root1']);
        $directory2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/root']);

        $directory1->addDirectory($directory2);
        $directory2->addDirectory($directory1);

        $parentDirectory1 = $directory1->getParent();
        $parentDirectory2 = $directory2->getParent();

        self::assertEmpty($parentDirectory2);
        self::assertSame($parentDirectory1, $directory2);

        $directory1->addDirectory($directory2);

        $parentDirectory1 = $directory1->getParent();
        $parentDirectory2 = $directory2->getParent();
        self::assertEmpty($parentDirectory1);
        self::assertSame($parentDirectory2, $directory1);
    }

    public function testGetDirectories()
    {
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $level1A = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);
        $level1B = $this->getMockForAbstractClass(AbstractDirectory::class, ['the/path/to/level1B']);
        $root->addDirectory($level1A);
        $root->addDirectory($level1B);
        $directories = $root->getDirectories();
        self::assertCount(2, $directories);
        self::assertArrayHasKey($level1A->getName(), $directories);
        self::assertArrayHasKey($level1B->getName(), $directories);

        self::assertSame($directories[$level1A->getName()], $level1A);
        self::assertSame($directories[$level1B->getName()], $level1B);
    }

    public function testGetDirectoryBy()
    {
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $level1A = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);
        $level1B = $this->getMockForAbstractClass(AbstractDirectory::class, ['the/path/to/level1B']);
        $root->addDirectory($level1A);
        $root->addDirectory($level1B);
        $level1AControl = $root->getDirectoryBy('level1A');
        $level1BControl = $root->getDirectoryBy('level1B');
        self::assertSame($level1A, $level1AControl);
        self::assertSame($level1B, $level1BControl);
    }

    public function testGetDirectoryByNotFound()
    {
        $this->expectException(NotFoundException::class);
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $level1A = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);
        $level1B = $this->getMockForAbstractClass(AbstractDirectory::class, ['the/path/to/level1B']);
        $root->addDirectory($level1A);
        $root->addDirectory($level1B);
        $root->getDirectoryBy('give me a non-existent directory');
    }

    public function testRemoveDirectory()
    {
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $level1A = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);
        $level1B = $this->getMockForAbstractClass(AbstractDirectory::class, ['the/path/to/level1B']);
        $root->addDirectory($level1A);
        $root->addDirectory($level1B);
        $root->removeDirectory($level1B);
        $directories = $root->getDirectories();
        self::assertCount(1, $directories);
        self::assertSame($level1A, $directories[$level1A->getName()]);

        self::assertEmpty($level1B->getParent());
    }

    public function testRemoveDirectoryExcept()
    {
        $this->expectException(TreeException::class);

        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $child = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);

        $root->removeDirectory($child);
    }

    public function testContainsDirectory()
    {
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $child = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);

        $root->addDirectory($child);

        self::assertTrue($root->containsDirectory($child->getName()));
        self::assertFalse($root->containsDirectory('non-existent directory'));
    }

    public function testRemoveFromParent()
    {
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $child = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);

        $root->addDirectory($child);
        $child->removeFromParent();

        $directories = $root->getDirectories();
        self::assertCount(0, $directories);
        self::assertEmpty($child->getParent());
    }

    public function testRemoveFromParentIsRoot()
    {
        $child = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);
        try {
            $child->removeFromParent();
        } catch (\Throwable $e) {
            $this->fail();
        }
        self::assertTrue(true);
    }

    public function testIsRoot()
    {
        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['root']);
        $child = $this->getMockForAbstractClass(AbstractDirectory::class, ['path/to/level1A']);

        $root->addDirectory($child);
        self::assertTrue($root->isRoot());
        self::assertFalse($child->isRoot());

        $child->removeFromParent();

        self::assertTrue($child->isRoot());
    }

    public function testContainsParent()
    {

        $root = $this->getMockForAbstractClass(AbstractDirectory::class, ['/root']);
        $child1 = $this->getMockForAbstractClass(AbstractDirectory::class, ['/child1']);
        $child2 = $this->getMockForAbstractClass(AbstractDirectory::class, ['/child1/child2']);
        $child3 = $this->getMockForAbstractClass(AbstractDirectory::class, ['/child3']);
        $child4 = $this->getMockForAbstractClass(AbstractDirectory::class, ['/child3/child4']);
        $child5 = $this->getMockForAbstractClass(AbstractDirectory::class, ['/child3/child4/child5']);
        $childL = $this->getMockForAbstractClass(AbstractDirectory::class, ['/child3/child4/childL']);

        $root->addDirectory($child1);
        $root->addDirectory($child3);
        $child1->addDirectory($child2);
        $child3->addDirectory($child4);
        $child4->addDirectory($child5);
        $child4->addDirectory($childL);

        self::assertTrue($child2->containsParent($root));
        self::assertTrue($childL->containsParent($child3));
        self::assertFalse($childL->containsParent($child1));
    }

}
