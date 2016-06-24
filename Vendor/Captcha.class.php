<?php
/**
 * 通用验证码生成类
 **/

class Captcha {

    // 资源
    private $captchaObj;

    // 默认配色
    private $colorScheme = [
            ['bg' => [70, 100, 100], 'ft' => [255, 255, 255]],
            ['bg' => [0, 0, 0], 'ft' => [255, 255, 255]],
            // ['bg' => [0, 0, 0], 'ft' => [0, 0, 0]],
            // ['bg' => [0, 0, 0], 'ft' => [0, 0, 0]],
            // ['bg' => [0, 0, 0], 'ft' => [0, 0, 0]],
            // ['bg' => [0, 0, 0], 'ft' => [0, 0, 0]],
            // ['bg' => [0, 0, 0], 'ft' => [0, 0, 0]],
            // ['bg' => [0, 0, 0], 'ft' => [0, 0, 0]],
            // ['bg' => [0, 0, 0], 'ft' => [0, 0, 0]],
            // ['bg' => [0, 0, 0], 'ft' => [0, 0, 0]],
        ];

    /**
     * 初始化
     * @param integer $width    [description]
     * @param integer $height   [description]
     * @param [type]  $fontSize [description]
     * @param float   $overlap  [description]
     * @param integer $angle    [description]
     */
    public function __construct($width = 80, $height = 35, $fontSize = null, $overlap = 0.7, $angle = 30){
        $this->captchaObj = new Captcha_GD($width, $height, $fontSize, $overlap, $angle);
        // Set path of captcha fonts
        $this->captchaObj->fontDir = __DIR__.'/CaptchaFonts/';
        // Set color scheme
        $this->captchaObj->colorScheme = $this->colorScheme;


    }

    /**
     * 输出图像
     * @param  string $code 输入验证码字符
     * @return
     */
    public function show($code){
        $this->captchaObj->show($code);
    }

    /**
     * 测试GD库和Imagick库
     * @return
     */
    public function test(){
        if(extension_loaded('gd')) {
            print_r(gd_info());
        }else {
            echo 'GD is not available.';
        }
        echo '<br>';
        if(extension_loaded('imagick')) {
            $imagick = new Imagick();
            print_r($imagick->queryFormats());
        }else {
            echo 'ImageMagick is not available.';

        }
    }

}


/**
 * GD验证码生成类
 **/
class Captcha_GD {

    /**
     * 配色方案
     **/
    public $colorScheme;

    /**
     * 字体包路径
     **/
    public $fontDir;


    /**
     * 画布资源
     **/
    private $image;

    /**
     * 验证码字符串
     **/
    private $code;

    /**
     * 字符串总数
     **/
    private $codeCount;

    /**
     * 当前配色方案
     **/
    private $color;

    /**
     * 图像宽度
     **/
    private $width;

    /**
     *图像高度
     **/
    private $height;

    /**
     * 字体大小
     **/
    private $fontSize;

    /**
     * 字体重叠比
     **/
    private $overlap;

    /**
     * 字体文件地址
     **/
    private $font;

    /**
     * 字体库
     **/
    private $fontArray;

    /**
     * 向左最大旋转角度
     **/
    private $maxAngle;

    /**
     * 向右旋转角度
     **/
    private $minAngle;

    public function __construct($width = 80, $height = 35, $fontSize = null, $overlap = 0.7, $angle = 30){
        $this->width = $width;
        $this->height = $height;
        $this->fontSize = $fontSize;
        $this->overlap = $overlap;
        $this->maxAngle = $angle;
        $this->minAngle = -$angle;
        // $this->fontDir = __DIR__.'/CaptchaFonts/';
        // $colorScheme = [];
    }

    public function show($code){
        $this->code = $code;

        /**
         * 创建图像
         */
        $this->create();

        /**
         * 设置header
         */
        $this->header();

        /**
         * 输出图像
         */
        imagepng($this->image);
    }

    /**
     * 设置header
     * @return
     */
    private function header(){
        header('Content-Type: image/png');
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        header('Expires: 0');
    }

    /**
     * 创建图像
     * @return
     */
    private function create(){

        /**
         * 随机配色
         */
        $this->color = $this->randomColorScheme();

        /**
         * 随机选取字体
         */
        $this->font = $this->randomFont();

        $this->image = imagecreate($this->width, $this->height);
        // 填充背景色
        imagecolorallocate($this->image, $this->color['bg'][0], $this->color['bg'][1], $this->color['bg'][2]);
        // 生成字体颜色
        $this->fontColor = imagecolorallocate($this->image, $this->color['ft'][0], $this->color['ft'][1], $this->color['ft'][2]);


        // 计算字符个数
        $this->codeCount = strlen($this->code);

        /**
         * 如果没有设置字体大小，则通过宽高及字符数自动计算
         */
        if($this->fontSize === null){
            $fontSize = $this->width / $this->codeCount;
            if($fontSize < $this->height){
                $this->fontSize = $fontSize * 1.2;
            }else{
                $this->fontSize = $this->height * 1.2;
            }
        }

        /**
         * 为每个字符单独设置旋转角度、大小等
         */
        // 初始化左边距
        $left = 0;
        for($i=0; $i<$this->codeCount; $i++){
            // 随机字体旋转角度
            $angle = rand($this->maxAngle, $this->minAngle);

            /**
             * 获取字符旋转后四个顶点坐标
             * 顶点坐标为(x,y)坐标点，GD库的展现区域为坐标轴的第四象限，
             * 所以如果映射到坐标轴上，图像的左上角为(0,0)点。
             *      ∧ y轴
             *      ┃
             *      ┃0,0
             * ━━━━━╋━━━━━━＞ x轴
             *      ┃GD图像区域
             *      ┃
             */
            $ttfbbox = imagettfbbox($this->fontSize, $angle, $this->font, $this->code[$i]);

            // 设置x轴、y轴变量
            $x = $y = [];

            // 计算字符在象限中的4个极值
            for($j=0; $j<8; $j++){
                /**
                 * 这里的判断依据为偶数键值为x坐标点，奇数为y坐标点
                 * 键从0开始依次为：左下、右下、右上、左上
                 */
                if($j%2 == 0){
                    if(!isset($x['min']) || ($ttfbbox[$j] < $x['min'])){
                        $x['min'] = $ttfbbox[$j];
                    }
                    if(!isset($x['max']) || ($ttfbbox[$j] > $x['max'])){
                        $x['max'] = $ttfbbox[$j];
                    }
                }else{
                    if(!isset($y['min']) || ($ttfbbox[$j] < $y['min'])){
                        $y['min'] = $ttfbbox[$j];
                    }
                    if(!isset($y['max']) || ($ttfbbox[$j] > $y['max'])){
                        $y['max'] = $ttfbbox[$j];
                    }
                }
            }

            // 计算图形边长
            $width = abs($x['min'] - $x['max']);
            $height = abs($y['min'] - $y['max']);

            // 计算图像边缘距离
            $top = ($this->height - $height) / 2 + abs($y['min']);
            if($left == 0){
                $left = abs($x['min']);
            }else{
                $left = ($left + $width * $this->overlap);
            }

            // 绘制字符
            $border = imagettftext($this->image, $this->fontSize, $angle, $left, $top, $this->fontColor, $this->font, $this->code[$i]);

            /**
             * 字符坐标排查，插入自定义header
             */
            // header('X-Original-Position: angle:'.$angle.' | width:'.$width.' | width:'.$height.' | left:'.$left.' | top:'.$top);
            // header('X-Original-Coordinate: (x:'.$border[0].',y'.$border[1].') | (x:'.$border[2].',y:'.$border[3].') | (x:'.$border[4].',y:'.$border[5].') | (x:'.$border[6].',y:'.$border[7].')');
            // header('X-test2: angle:'.$angle.' | width:'.$width.' | width:'.$height.' | left:'.$left.' | top:'.$top.' |$| (x'.$x['min'].',y'.$y['min'].') | (x:'.$x['max'].',y:'.$y['min'].') | (x:'.$x['max'].',y:'.$y['max'].') | (x:'.$x['min'].',y:'.$y['max'].')');
            // header('X-font: '.$this->font);

            /**
             * 字符位置辅助代码
             * 通过字符长宽计算真实坐标
             */
            // $x['min'] = $x['min'] + $left;
            // $x['max'] = $x['max'] + $left;
            // $y['min'] = $y['min'] + $top;
            // $y['max'] = $y['max'] + $top;
            // $borderColor = imagecolorallocate($this->image, (255 - $this->color['bg'][0]), 255 - ($this->color['bg'][1]), (255 - $this->color['bg'][2]));
            // imagepolygon($this->image, $border, 4, $borderColor);
            // imagepolygon($this->image, [$x['min'], $y['min'], $x['max'], $y['min'], $x['max'], $y['max'], $x['min'], $y['max']], 4, $borderColor);
        }
    }

    /**
     * 随机配色方案
     * @return array 返回配色方案数组
     */
    private function randomColorScheme(){
        return $this->colorScheme[array_rand($this->colorScheme)];
    }

    /**
     * 随机字体
     * @return string 返回字体路径
     */
    private function randomFont(){
        if(empty($this->fontArray)){
            if(false != ($handle = opendir($this->fontDir))){
                $i=0;
                while(false !== ($file = readdir($handle))){
                    // 只保留ttf后缀文件
                    if(stripos($file, '.ttf')){
                        $fileArray[$i] = $this->fontDir.$file;
                        $i++;
                    }
                }
                closedir($handle);
            }
        }
        return $fileArray[array_rand($fileArray)];
    }

    /**
     * 注销资源
     */
    public function __destruct(){
        if($this->image){
            imagedestroy($this->image);
        }
    }

}


/**
 * Imagic验证码生成类
 **/
class Captcha_Imagick {

}
