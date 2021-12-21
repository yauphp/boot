<?php

namespace Yauphp\Boot;

use Yauphp\Core\IRunnable;
use Yauphp\Core\ClassLoader;
use Yauphp\Config\ConfigurationFactory;

/**
 * 引导程序
 * @author Tomix
 *
 */
class Bootstrap
{
    private $configFile;
    private $baseDir;
    private $userDir;
    private $extConfigs=[];
    private $targetObjectId;
    private $configurationFactoryDebug=false;
    private $configurationFactoryCacheDir;
    private $classMap=[];

    private static $singleton;

    /**
     * 构造函数
     */
    private function __construct(){

    }

    /**
     * 执行一个可执行对象
     * @param IRunnable $target
     */
    public static function run(IRunnable $target)
    {
        $target->run();
    }

    /**
     * 准备一个实例
     * @return \Yauphp\Boot\Bootstrap
     */
    public static function ready(){

        if(self::$singleton){
            self::$singleton=new Bootstrap();
        }
        return self::$singleton;
    }


    /**
     * 创建可执行应用实例
     * @return IRunnable
     */
    public function build(){

        //class loader
        if(!empty($this->classMap)){
            ClassLoader::load($this->classMap);
        }

        //配置工厂静态属性
        ConfigurationFactory::setDebug($this->configurationFactoryDebug);
        if(!empty($this->configurationFactoryCacheDir)){
            ConfigurationFactory::setCacheDir($this->configurationFactoryCacheDir);
        }

        //使用配置工厂创建配置实例
        $config=ConfigurationFactory::create($this->configFile,$this->baseDir,$this->userDir,$this->extConfigs);

        //使用配置实例获取对象工厂
        $objectFactory=$config->getObjectFactory();

        //使用对象工厂创建可运行应用实例
        $target=$objectFactory->create($this->targetObjectId);

        return $target;

    }

    /**
     * 引导
     */
    public function boot(){

        $target=$this->build();
        self::run($target);
    }

    /**
     * 配置文件位置
     * @param string $configFile
     * @return Bootstrap
     */
    public function configFile($configFile) : Bootstrap{

        $this->configFile=$configFile;
        return $this;
    }

    /**
     * 应用根目录
     * @param string $baseDir
     * @return Bootstrap
     */
    public function baseDir($baseDir) : Bootstrap{

        $this->baseDir=$baseDir;
        return $this;
    }

    /**
     * 用户根目录
     * @param string $userDir
     * @return Bootstrap
     */
    public function userDir($userDir) : Bootstrap{

        $this->userDir=$userDir;
        return $this;
    }

    /**
     * 附加配置数据
     * @param array $extConfigs
     * @return Bootstrap
     */
    public function extConfigs(array $extConfigs) : Bootstrap{

        $this->extConfigs=$extConfigs;
        return $this;
    }

    /**
     * 启动入口应用的对象ID
     * @param string $targetObjectId
     * @return Bootstrap
     */
    public function targetObjectId($targetObjectId) : Bootstrap{

        $this->targetObjectId=$targetObjectId;
        return $this;
    }

    /**
     * 设置配置工厂是否调试模式
     * @param bool $configurationFactoryDebug
     * @return Bootstrap
     */
    public function configurationFactoryDebug(bool $configurationFactoryDebug) : Bootstrap{

        $this->configurationFactoryDebug=$configurationFactoryDebug;
        return $this;
    }

    /**
     * 设置配置工厂的缓存位置
     * @param string $configurationFactoryCacheDir
     * @return Bootstrap
     */
    public function configurationFactoryCacheDir($configurationFactoryCacheDir) : Bootstrap{

        $this->configurationFactoryCacheDir=$configurationFactoryCacheDir;
        return $this;
    }

    /**
     * 设置类加载映射,键为命名空间前缀,值为路径
     * @param array $classMap
     * @return Bootstrap
     */
    public function classMap(array $classMap) : Bootstrap{

        $this->classMap=$classMap;
        return $this;
    }

    /**
     * 添加类加载映射
     * @param string $prefix 命名空间前缀
     * @param string $path   路径
     * @return Bootstrap
     */
    public function addClassMap($prefix,$path) : Bootstrap{

        $this->classMap[$prefix]=$path;
        return $this;
    }
}

