<?php

use JetBrains\PhpStorm\Language;


	
$router->get('languages/', 'LanguageController@language');
$router->post('test_solr/', 'LanguageController@test_solr');
$router->get('languages/all', 'LanguageController@allLanguages');
$router->get('categories/', 'CategoryController@index');
$router->get('divisions/', 'AdminSettingsController@divisions');
$router->get('districts/{division_id}', 'AdminSettingsController@districts');
$router->get('upazilaInfo/{upazila_id}', 'AdminSettingsController@upazilaInfo');
$router->get('upazilas/{district_id}', 'AdminSettingsController@upazilas');
$router->get('dashboard/stats', 'AdminSettingsController@stats');
$router->get('education/levels', 'EducationController@levels');
$router->get('employment/professions', 'ProfessionController@professions');
$router->get('education/degreeBylevel/{level_id}', 'DegreeController@degreeBylevel');
$router->get('employment/fieldByprofession/{profession_id}', 'ProfessionController@fieldByprofession');
$router->get('slider/', 'SliderController@index');
$router->get('participant_types/', 'AdminSettingsController@participant_types');

$router->post('tags/search', 'TagsController@search');






$router->group(['middleware' => []], function () use ($router) {
	 
	 $router->post('language/create', 'LanguageController@addLang');
	 $router->put('language/update', 'LanguageController@updateLang');
	 $router->delete('language/{id}', 'LanguageController@deleteLang');

	 //role create
	 $router->post('new-role/create', 'RoleController@create');
	 $router->get('role-list/view', 'RoleController@rolelist');
	 $router->get('manage-roles/view', 'RoleController@serviceroles');
	 $router->get('manage-roles/show/{role}', 'RoleController@show');
	 $router->get('manage-role/view-full-schema','RoleController@schemas');
	 
	//  Language values
	 $router->get('language/values', 'LanguageController@langValues');
	 $router->post('language/value/create', 'LanguageController@addLangValue');
	 $router->put('language/value/update', 'LanguageController@updateLangValue');
	 $router->delete('language/value/{id}', 'LanguageController@deleteLangValue');
	 
	 // category
	 $router->post('categories/create', 'CategoryController@store');
	 $router->put('categories/update', 'CategoryController@update');
	 $router->delete('categories/{id}', 'CategoryController@destroy');
	 


	 // education level
	 $router->post('education/levels/create', 'EducationController@store');
	 $router->put('education/levels/update', 'EducationController@update');
	 $router->delete('education/levels/{id}', 'EducationController@destroy');

	$router->get('education/sublevel', 'EduSubLevelController@index');
	$router->get('education/levels-with-sub', 'EduSubLevelController@levelsWithSub');
	$router->post('education/sublevel/create', 'EduSubLevelController@store');
	$router->put('education/sublevel/update', 'EduSubLevelController@update');
	$router->delete('education/sublevel/{id}', 'EduSubLevelController@destroy');

	
	 
	 $router->post('employment/profession/create', 'ProfessionController@store');
	 $router->put('employment/profession/update', 'ProfessionController@update');
	 $router->delete('employment/profession/{id}', 'ProfessionController@destroy');

	 //degree
	 $router->post('degree/create', 'DegreeController@store');
	 $router->put('degree/update', 'DegreeController@update');
	 $router->delete('degree/{id}', 'DegreeController@destroy');
	 
	 
	 $router->post('working-field/create', 'WorkingFieldController@store');
	 $router->put('working-field/update', 'WorkingFieldController@update');
	 $router->delete('working-field/{id}', 'WorkingFieldController@destroy');

	 //
	 $router->get('disabilities/view','CategoryController@disabilities');

	 $router->get('slider/front', 'SliderController@front');
	 $router->get('slider/all', 'SliderController@allSlider');
	 $router->post('slider/create', 'SliderController@store');
	 $router->put('slider/update', 'SliderController@update');
	 $router->delete('slider/{id}', 'SliderController@destroy');
	 
    });
