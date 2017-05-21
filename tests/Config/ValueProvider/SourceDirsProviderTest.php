<?php

declare(strict_types=1);

namespace Tests\Config\ValueProvider;

use Infection\Config\ConsoleHelper;
use Infection\Config\ValueProvider\SourceDirsProvider;
use Mockery;

class SourceDirsProviderTest extends AbstractBaseProviderTest
{
    public function test_it_uses_guesser_and_default_value()
    {
        $consoleMock = Mockery::mock(ConsoleHelper::class);
        $consoleMock->shouldReceive('getQuestion')->once()->andReturn('?');

        $dialog = $this->getQuestionHelper();

        $provider = new SourceDirsProvider($consoleMock, $dialog);

        $sourceDirs = $provider->get(
            $this->createStreamableInputInterfaceMock($this->getInputStream("\n")),
            $this->createOutputInterface(),
            ['src'],
            'phpunit'
        );

        $this->assertSame(['src'], $sourceDirs);
    }

    public function test_it_fills_choices_with_current_dir()
    {
        $consoleMock = Mockery::mock(ConsoleHelper::class);
        $consoleMock->shouldReceive('getQuestion')->once()->andReturn('?');

        $dialog = $this->getQuestionHelper();

        $provider = new SourceDirsProvider($consoleMock, $dialog);

        $sourceDirs = $provider->get(
            $this->createStreamableInputInterfaceMock($this->getInputStream("0\n")),
            $this->createOutputInterface(),
            ['src']
        );

        $this->assertSame(['.'], $sourceDirs);
    }

    /**
     * @expectedException \LogicException
     */
    public function test_it_throws_exception_when_current_dir_is_selected_with_another_dir()
    {
        $consoleMock = Mockery::mock(ConsoleHelper::class);
        $consoleMock->shouldReceive('getQuestion')->once()->andReturn('?');

        $dialog = $this->getQuestionHelper();

        $provider = new SourceDirsProvider($consoleMock, $dialog);

        $provider->get(
            $this->createStreamableInputInterfaceMock($this->getInputStream("0,1\n")),
            $this->createOutputInterface(),
            ['src'],
            'phpunit'
        );
    }
}