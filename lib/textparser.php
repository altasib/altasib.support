<?
#################################################
#        Company developer: ALTASIB
#        Developer: Evgeniy Pedan
#        Site: http://www.altasib.ru
#        E-mail: dev@altasib.ru
#        Copyright (c) 2006-2015 ALTASIB
#################################################
?>
<?
namespace ALTASIB\Support;

class TextParser extends \CTextParser
{
	function convert_image_tag($url = "", $params = "")
	{
		$url = trim($url);
		if (strlen($url)<=0)
			return '';

		preg_match("/width\\=([0-9]+)/is".BX_UTF_PCRE_MODIFIER, $params, $width);
		preg_match("/height\\=([0-9]+)/is".BX_UTF_PCRE_MODIFIER, $params, $height);
		$width = intval($width[1]);
		$height = intval($height[1]);

		$bErrorIMG = false;
		if (!$bErrorIMG && !preg_match("/^(http|https|ftp|\\/)/i".BX_UTF_PCRE_MODIFIER, $url))
			$bErrorIMG = true;

		$url = htmlspecialcharsbx($url);
		if ($bErrorIMG)
			return "[img]".$url."[/img]";

		$strPar = " style=\"";
		if($width > 0)
		{
			if($width > $this->imageWidth)
			{
				$height = intval($height * ($this->imageWidth / $width));
				$width = $this->imageWidth;
			}
		}
		if($height > 0)
		{
			if($height > $this->imageHeight)
			{
				$width = intval($width * ($this->imageHeight / $height));
				$height = $this->imageHeight;
			}
		}
		if($width > 0)
			$strPar .= "max-width:".$width."px;";
		if($height > 0)
			$strPar .= " max-height:".$height."px;";

        $strPar .= "\"";
		$image = '<img src="'.$this->serverName.$url.'" border="0"'.$strPar.' data-bx-image="'.$this->serverName.$url.'" />';
		if(strlen($this->serverName) <= 0 || preg_match("/^(http|https|ftp)\\:\\/\\//i".BX_UTF_PCRE_MODIFIER, $url))
			$image = '<img src="'.$url.'" border="0"'.$strPar.' data-bx-image="'.$url.'" />';
		return $this->defended_tags($image, 'replace');
	}
}    
?>    