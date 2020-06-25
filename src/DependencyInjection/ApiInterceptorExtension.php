<?php


namespace FreedomSex\ApiInterceptorBundle\DependencyInjection;


use FreedomSex\ApiInterceptorBundle\Contract\InterceptorInterface;
use FreedomSex\ApiInterceptorBundle\Services\Handler\PreSerializeHandlerInterface;
use FreedomSex\ApiInterceptorBundle\Services\Worker\WorkerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class ApiInterceptorExtension extends Extension implements ExtensionInterface, CompilerPassInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(PreSerializeHandlerInterface::class)
            ->addTag('itr.serializer.pre_handler');
        $container->registerForAutoconfiguration(InterceptorInterface::class)
            ->addTag('interceptor');
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('interceptor') as $serviceId => $tags) {
            $container->getDefinition($serviceId)->setPublic(true);
        }

        $definition = $container->findDefinition('FreedomSex\ApiInterceptorBundle\Services\Handler\PreSerializeCollector');
        $services = $container->findTaggedServiceIds('itr.serializer.pre_handler');

        foreach ($services as $id => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($id)]);
        }
    }
}
