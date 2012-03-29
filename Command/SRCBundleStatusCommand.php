<?php

namespace BRS\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SRCBundleStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('src:status')
            ->setDescription('git status of all registered src bundles');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $wd = exec('pwd');
			
        $bundles = $this->getContainer()->getParameter('src_bundles');
		
		foreach($bundles as $vendor => $vendor_bundles){
			
			//exec("cd $wd/src/$vendor");
			
			foreach($vendor_bundles as $bundle_name => $git_location ){
				
				$output->writeln('Updating : ' . $bundle_name . ' from: ' . $git_location);
				
				passthru("cd $wd/src/$vendor/$bundle_name; git status;");
				
				//passthru("git status");
			}	
		}
    }
}