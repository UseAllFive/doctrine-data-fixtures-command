<?php

namespace UseAllFive\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Based on the Symfony 2 DoctrineFixturesBundle.
 * The difference here is that the Symfony 2 version
 * is able to load fixtures from its registered bundles'
 * respective paths, which naturally doesn't work if
 * not using Symfony 2.
 */
class LoadDataFixturesDoctrineCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fixtures:load')
            ->setDescription('Load data fixtures to your database.')
            ->addArgument(
                'fixtures-path',
                InputArgument::REQUIRED,
                'The directory or file to load data fixtures from.'
            )
            ->addOption(
                'append',
                null,
                InputOption::VALUE_NONE,
                'Append the data fixtures instead of deleting all data from the database first.'
            )
            ->addOption(
                'purge-with-truncate',
                null,
                InputOption::VALUE_NONE,
                'Purge data by using a database-level TRUNCATE statement'
            )
            ->setHelp(
<<<EOT
The <info>fixtures:load</info> command loads data fixtures from the specified path:

  <info>doctrine fixtures:load</info>

If you want to append the fixtures instead of flushing the database first you can use the <info>--append</info> option:

  <info>doctrine fixtures:load --append</info>

By default Doctrine Data Fixtures uses DELETE statements to drop the existing rows from
the database. If you want to use a TRUNCATE statement instead you can use the <info>--purge-with-truncate</info> flag:

  <info>doctrine fixtures:load --purge-with-truncate</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getHelper('em')->getEntityManager();

        $dirOrFile = $input->getArgument('fixtures-path');
        if ($dirOrFile) {
            $paths = is_array($dirOrFile) ? $dirOrFile : array($dirOrFile);
        } else {
            throw new InvalidArgumentException("Please provide datafixtures path");
        }

        $loader = new Loader();
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }
        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- ".implode("\n- ", $paths))
            );
        }
        $purger = new ORMPurger($em);
        $purger->setPurgeMode(
            ($input->getOption('purge-with-truncate'))
            ? ORMPurger::PURGE_MODE_TRUNCATE
            : ORMPurger::PURGE_MODE_DELETE
        );
        $executor = new ORMExecutor($em, $purger);
        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($fixtures, $input->getOption('append'));
    }
}
