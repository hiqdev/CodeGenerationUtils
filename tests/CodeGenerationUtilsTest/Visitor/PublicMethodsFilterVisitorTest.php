<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

declare(strict_types=1);

namespace CodeGenerationUtilsTest\Visitor;

use CodeGenerationUtils\Visitor\PublicMethodsFilterVisitor;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(PublicMethodsFilterVisitor::class)]
class PublicMethodsFilterVisitorTest extends TestCase
{
    /** @dataProvider nodeProvider */
    #[DataProvider('nodeProvider')]
    public function testRemovesOnlyPrivateMethods(Node $node, int|null $expected): void
    {
        $visitor = new PublicMethodsFilterVisitor();

        self::assertSame($expected, $visitor->leaveNode($node));
    }

    /** @psalm-return non-empty-list<array{ClassMethod|Class_, int|null}> */
    public static function nodeProvider(): array
    {
        return [
            [
                new ClassMethod(
                    'foo',
                    ['flags' => Modifiers::PUBLIC],
                ),
                null,
            ],
            [
                new ClassMethod(
                    'foo',
                    ['flags' => Modifiers::PROTECTED],
                ),
                NodeVisitor::REMOVE_NODE,
            ],
            [
                new ClassMethod(
                    'foo',
                    ['flags' => Modifiers::PRIVATE],
                ),
                NodeVisitor::REMOVE_NODE,
            ],
            [new Class_('foo'), null],
        ];
    }
}
