function catids ($field, $value) {
	
	$setting = $this->fields[$field]['setting'];
	$setting = string2array($setting);
	if ($setting['boxtype'] == 'multiple') {
		$CATEGORY = getcache('category_yp_'.$this->modelid, 'yp');
		$data = ',';
		foreach ($value as $catid) {
			$data .= $CATEGORY[$catid]['catname'].',';
		}
		return $data;
	} else {
		return $value;
	}
}