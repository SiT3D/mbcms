
namespace {{standart_namespace}};

// файл на который опирается ЦМС при создании шаблонов, это шаблон кода
// этот шаблон генерируется динамически! не пытайтесь его изменять!!!

class {{idTemplate_class}} extends \{{standart_namespace}}\tblock
{

public static $testdata = [
'idTemplate'   => {{idTemplate}},
'name'         => {{name}},
'title'        => {{title}},
'description'  => {{description}},
'path'         => {{path}},
'settingsData' => {{settingsData}},
'outputs'      => {{outputs}},
'childrens'    => {{childrens}},
];

}