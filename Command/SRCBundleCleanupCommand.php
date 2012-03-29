<?php

namespace BRS\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SRCBundleCleanupCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('src:clean')
			->setDescription('clean misc tmp files from src bundles');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$wd = exec('pwd');
			
		passthru("find . -name '*.DS_Store' -type f -delete");
	}
}