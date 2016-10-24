<?php
/**
 * @since  2016-03-21
 */

namespace EchartsBuilder;

/**
 * Class Config
 *
 * @package EchartsBuilder
 */
class Config {
    /**
     * @var string
     */
    public static $dist = 'https://cdnjs.cloudflare.com/ajax/libs/echarts/2.2.7';

    /**
     * @var array
     */
    public static $method = array();

    /**
     * @var bool
     */
    public static $isOutputJs = false;

    /**
     * @return array
     */
    public static function scriptCdn() {
        return array(
            'bar' => 'chart/bar.js',
        );
    }

    /**
     * @param $value
     * @return string
     */
    private static function _jsMethod($value) {
        $md5 = '{%' . md5($value) . '%}';
        self::$method['"' . $md5 . '"'] = $value;
        return $md5;
    }

    /**
     * 替换js的function
     * @param $option
     */
    private static function _optionMethod(&$option) {
        foreach($option as $key => $val) {
            if(is_string($val)) {
                $replace = str_replace(array("\t","\r","\n","\0","\x0B", ' '), '', $val);
                if(strpos($replace, 'function(') === 0) {
                    $option[$key] = self::_jsMethod($val);
                }
            } else if(is_array($val)) {
                self::_optionMethod($option[$key]);
            }
        }
    }

    /**
     * @param $option
     * @return mixed|string
     */
    private static function _jsonEncode($option) {
        $option = json_encode($option);
        if(self::$method) {
            $option = str_replace(array_keys(self::$method),array_values(self::$method),$option);
        }
        return $option;
    }

    /**
     * @param $option
     * @return string
     */
    private static function _require($option) {
        $requireString = "'echarts',";
        if(isset($option['series'])) {
            foreach($option ['series'] as $val) {
                if(isset($val['type'])) {
                    $requireString .= "'echarts/chart/" . $val['type'] . "',";
                }
            }
            $requireString = rtrim($requireString,',');
        }
        return $requireString;
    }

    /**
     * @param array $attribute
     * @return string
     */
    private static function _renderAttribute(array $attribute = array()) {
        $attributeString = '';
        if(!isset($attribute['style'])) {
            $attribute['style'] = 'height:400px';
        }
        foreach($attribute as $key => $val) {
            $attributeString .= " $key=\"" . htmlspecialchars($val,ENT_QUOTES,'utf-8') . '"';
        }
        return $attributeString;
    }

    public static function render($id,$option,$theme = null,array $attribute = array()) {
        self::_optionMethod($option);
        $dist = self::$dist;
        $require = self::_require($option);
        $option = self::_jsonEncode($option);
        $attribute = self::_renderAttribute($attribute);
        is_null($theme) && $theme = 'null';
        if(!self::$isOutputJs) {
            $js = '<script src="' . $dist . '/echarts.js"></script>';
            self::$isOutputJs = true;
        } else {
            $js = '';
        }
            return <<<HTML
<div id="$id" $attribute></div>
$js
<script type="text/javascript">
	require.config({
		paths: {
			echarts: '{$dist}'
		}
	});
	require(
		[
			$require
		],
		function (ec) {
			var myChart = ec.init(document.getElementById('$id'), $theme);
			var option = $option;
			myChart.setOption(option);
		}
	);
</script>
HTML;
    }
}