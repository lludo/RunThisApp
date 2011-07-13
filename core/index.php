<?php 

/*
 *    RunThisApp allows sharing test builds of iOS apps with testers.
 *    Copyright (C) 2011 Ludovic Landry & Pascal Cans
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
 //TODO rename this file to initDoctrine.php

use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ApcCache;

function initDoctrine() {
	
	require_once __DIR__ . '/../lib/vendor/doctrine-common/lib/Doctrine/Common/ClassLoader.php';
	
	// Set up class loading. You could use different autoloaders, provided by your favorite framework,
	// if you want to.
	$classLoader = new ClassLoader('Doctrine\ORM', realpath(__DIR__ . '/../lib'));
	$classLoader->register();
	$classLoader = new ClassLoader('Doctrine\DBAL', realpath(__DIR__ . '/../lib/vendor/doctrine-dbal/lib'));
	$classLoader->register();
	$classLoader = new ClassLoader('Doctrine\Common', realpath(__DIR__ . '/../lib/vendor/doctrine-common/lib'));
	$classLoader->register();
	$classLoader = new ClassLoader('Symfony', realpath(__DIR__ . '/../lib/vendor'));
	$classLoader->register();
	$classLoader = new ClassLoader('Entities', __DIR__);
	$classLoader->register();
	$classLoader = new ClassLoader('Proxies', __DIR__);
	$classLoader->register();
	
	// Set up caches
	$config = new Configuration;
	$cache = new ApcCache;
	$config->setMetadataCacheImpl($cache);
	$driverImpl = $config->newDefaultAnnotationDriver(array(__DIR__ . "/Entities"));
	$config->setMetadataDriverImpl($driverImpl);
	$config->setQueryCacheImpl($cache);
	
	// Proxy configuration
	$config->setProxyDir(__DIR__ . '/Proxies');
	$config->setProxyNamespace('Proxies');
	$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
	
	// Database connection information
	$connectionOptions = array(
	    'driver' => 'pdo_sqlite',
	    'path' => __DIR__ . '/database.sqlite'
	);
	
	// Create EntityManager
	$em = EntityManager::create($connectionOptions, $config);
	return $em;
}

## PUT YOUR TEST CODE BELOW
/*
$em = initDoctrine();

$developer = new Developer;
$developer->setFirstName('Ludovic');
$developer->setLastName('Landry');
$em->persist($developer);

$developer = new Developer;
$developer->setFirstName('Pascal');
$developer->setLastName('Cans');
$em->persist($developer);

$em->flush();

echo "Developer saved!" . PHP_EOL;
*/
