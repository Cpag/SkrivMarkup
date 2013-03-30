<?php
namespace Skriv\Extensions;
use Skriv\Markup\SkrivInlineExtension;

class LipsumInlineExtension implements SkrivInlineExtension {

	private static $content = array(
		'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi.',
		'Proin porttitor, orci nec nonummy molestie, enim est eleifend mi, non fermentum diam nisl sit amet erat. Duis semper. Duis arcu massa, scelerisque vitae, consequat in, pretium a, enim. Pellentesque congue. Ut in risus volutpat libero pharetra tempor. Cras vestibulum bibendum augue.',
		'Praesent egestas leo in pede. Praesent blandit odio eu enim. Pellentesque sed dui ut augue blandit sodales. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aliquam nibh. Mauris ac mauris sed pede pellentesque fermentum. Maecenas adipiscing ante non diam sodales hendrerit. Ut velit mauris, egestas sed, gravida nec, ornare ut, mi.',
		'Aenean ut orci vel massa suscipit pulvinar. Nulla sollicitudin. Fusce varius, ligula non tempus aliquam, nunc turpis ullamcorper nibh, in tempus sapien eros vitae ligula. Pellentesque rhoncus nunc et augue. Integer id felis. Curabitur aliquet pellentesque diam. Integer quis metus vitae elit lobortis egestas. Lorem ipsum dolor sit amet, consectetuer adipiscing elit.',
		'Morbi vel erat non mauris convallis vehicula. Nulla et sapien. Integer tortor tellus, aliquam faucibus, convallis id, congue eu, quam. Mauris ullamcorper felis vitae erat. Proin feugiat, augue non elementum posuere, metus purus iaculis lectus, et tristique ligula justo vitae magna.',
		'Aliquam convallis sollicitudin purus. Praesent aliquam, enim at fermentum mollis, ligula massa adipiscing nisl, ac euismod nibh nisl eu lectus. Fusce vulputate sem at sapien. Vivamus leo. Aliquam euismod libero eu enim. Nulla nec felis sed leo placerat imperdiet. Aenean suscipit nulla in justo. Suspendisse cursus rutrum augue. Nulla tincidunt tincidunt mi.',
		'Curabitur iaculis, lorem vel rhoncus faucibus, felis magna fermentum augue, et ultricies lacus lorem varius purus. Curabitur eu amet.');

	public function getConf() {
		return array('name' => 'lipsum', 'parameters-type' => 'listed');
	}
	public function to($type, array $params = null) {
		if (count($params) !== 1)
			throw new \Exception('Invalid syntax: one parameter is expected');
		if (!is_numeric($params[0]))
			throw new \Exception('Invalid syntax: the parameter must be an integer');
		$nb = intval($params[0]);
		$slice = array_slice(self::$content, 0, $nb);
		if (empty($slice))
			return '';
		switch ($type) {
			case 'html':
				return $this->toHtml($slice);
			case 'plain-text':
				return $this->toPlainText($slice);
			default:
				throw new \Exception('Unknown type "' . $type . '"');
		}
	}
	private function toHtml(array $slice) {
		return '<p>' . implode("</p>\n<p>", $slice) . '</p>';
	}
	private function toPlainText(array $slice) {
		return implode("\n\n", $slice);
	}
}
