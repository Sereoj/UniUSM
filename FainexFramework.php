<?php
function TSynT( $file ,$synEdit)
{
    $hi = new TSynPHPSyn (_c($synEdit->owner));
    $hi->name = 'SynPHPSyn';

    $colors = json_decode (file_get_contents ($file), true);
    $hi->loadFromArray ($colors);


    gui_readStr($synEdit->self, '
            object synedit1: TSynEdit
                   Highlighter = Form1.SynPHPSyn
            end');
}

function Label()
{
    $FLabel = new FLabel(c("Form1"),
        array (
            'label' => array
            (
                'autoSize' => false,
                'anchors' => 'akRight,akBottom',
                'x' => 858,
                'y' => 580,
                'w' => 24,
                'h' => 24,

                'caption' => ".:"
            )
        )
    );

    $FLabel->onMousedown = function ($self) use ($FLabel)
    {
        $FLabel->caption = ".:";
        //func()->ReleaseCapture();
        //c("Form1")->perform(0x112, 0xF008, 0);
    };
    $FLabel->onMouseup = function ($self) use ($FLabel)
    {
        $FLabel->caption = ".:";
    };
    unset($FLabel);
}


Global $FainexObjects;


$screen = new TForm;

$screen->borderStyle     = bsNone;
$screen->windowState     = wsMaximized;
$screen->alphaBlend      = true;
$screen->alphaBlendValue = 0;

$screen->show ();

define ('clDarkGray',       1907997);
define ('clDark2Gray',      1118481);
define ('clBackgroundGray', 1710618);
define ('clLightGray',      2763306);
define ('clLightBlue',      13275648);
define ('clLight',          15658734);

define ('SCREEN_WIDTH',  $screen->width);
define ('SCREEN_HEIGHT', $screen->height);

$screen->hide ();
$screen->free ();
unset ($screen);

function setObjectProperties ($object, $properties = array ()) // Установка свойств объекту
{
    if (is_array ($object))
        foreach ($object as $id => $obj)
            setObjectProperties ($obj, $properties[$id]);

    else foreach ($properties as $propertyName => $propertyValue)
        if (is_array ($propertyValue))
            $object->$propertyName = setObjectProperties ($object->$propertyName, $propertyValue);

        else
        {
            if (!is_object ($object))
                $object = c($object);

            $object->$propertyName = $propertyValue;
        }

    return $object;
}

function getFainexObject ($fid) // Получение Fainex-компонента по его ID
{
    Global $FainexObjects;

    return (isset ($FainexObjects[$fid]) ? $FainexObjects[$fid] : false);
}

function searchFinexObject ($object, $searchProperty = 'self') // Поиск Fainex-компонента по содержащимся в нём обычным компонентам
{
    Global $FainexObjects;

    if (is_object ($object))
        $object = $object->$searchProperty;

    foreach ($FainexObjects as $fid => $FainexObject)
        foreach ($FainexObject as $name => $obj)
            if (is_object ($obj) && isset ($obj->$searchProperty) && $obj->$searchProperty == $object)
                return $FainexObject;
}

function normilizeInt ($int, $begin = 0, $end = 100) // Нормализация числа по промежутку
{
    if ($int < $begin)
        return $begin;

    elseif ($int > $end)
        return $end;
    
    else return $int;
}

function build_query ($params) // Билдер GET-запросов
{
    foreach ($params as $id => $value)
        $pars[] = urlencode ($id) .'='. urlencode ($value);

    return (is_array ($pars) ? implode ('&', $pars) : '');
}

function get_request ($url, $params, $end = '') // Функция работы с GET-запросами через CURL
{
    if (function_exists ('curl_init') && $curl = curl_init ())
    {
        if (is_array ($params))
            $pars = build_query ($params);

        curl_setopt ($curl, CURLOPT_URL, $url .'?'. $pars . $end);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($curl, CURLOPT_HEADER, 1);

        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, false);

        $out = curl_exec ($curl);

        curl_close ($curl);

        preg_match_all ('/{(.*)}/', $out, $preg);

        return (($out === false) ? curl_error ($curl) : json_decode ($preg[0][0], true));
    }
    
    else return false;
}

abstract class FVisual
{
    public $fid;    // Fainex ID - ID компонента
    public $parent; // Родитель компонента

    public function __construct ($parent = null, $inProperties = array (), $defProperties = array ()) // Создание компонента
    {
        foreach (array ($defProperties, $inProperties) as $pid => $properties)
            self::setProperties ($properties);

        $this->parent = $parent;
        self::registerObject ();
    }

    public function __set ($name, $value)
    {
        if (is_callable ($value))
        {
            $object = current ($this);

            $object->$name = $value;
        }

        else foreach ((array) $this as $objectName => $object)
            if (is_object ($object) && $objectName != 'parent')
            {
                if (isset ($object->self))
                    $object->$name = $value;

                elseif (isset ($object->fid) && method_exists ($object, 'setProperties'))
                    $object->$name = $value;
            }
    }

    public function setProperties ($properties)
    {
        $properties = self::convertProperties ($properties);

        setObjectProperties (array_keys ($properties), array_values ($properties));
    }

    protected function convertProperties ($properties)
    {
        foreach ($properties as $id => $value)
        {
            if (is_object ($this->$id)) // Объект
            {
                if (!isset ($this->$id->self))
                {
                    foreach ($this->$id as $iid => $vvalue)
                        if (is_object ($vvalue) && isset ($vvalue->self))
                        {
                            $properties[$vvalue->self] = $value;

                            break;
                        }
                }
                
                else $properties[$this->$id->self] = $value;

                unset ($properties[$id]);
            }

            elseif (strpos ($id, '->')) // Путь до составного объекта
            {
                $objs   = explode ('->', $id);
                $object = $this;

                foreach ($objs as $oid => $ovl)
                    $object = $object->$ovl;

                if (isset ($object->self))
                    $properties[$object->self] = $value;

                unset ($properties[$id]);
            }
        }

        return $properties;
    }

    protected function registerObject () // Регистрация компонента в списке
    {
        Global $FainexObjects;

        while (!$this->fid || (isset ($FainexObjects[$this->fid]) && $FainexObjects[$this->fid] != $this))
            $this->fid = dechex ((time () + crc32 ($this->fid) + (++$i)));

        $FainexObjects[$this->fid] = $this;
    }

    protected function unregisterObject () // Удаление компонента из списка
    {
        Global $FainexObjects;

        if ($FainexObjects[$this->fid] != $this)
            foreach ($FainexObjects as $fid => $object)
                if ($object == $this)
                    unset ($FainexObjects[$fid]);

        else unset ($FainexObjects[$this->fid]);
    }

    public function remove () // Удаление компонента
    {
        foreach ((array) $this as $id => $property)
            if (!is_a ($property, 'TForm'))
            {
                if (method_exists ($property, 'remove'))
                    $property->remove ();
                
                elseif (method_exists ($property, 'free'))
                    $property->free ();

                else unset ($this->$id);
            }

        self::unregisterObject ();
    }

    protected function __destruct () // Уничтожение компонента
    {
        self::remove ();
    }
}

class FCollusion extends FVisual
{
    public $shape;

    public function __construct ($parent, $properties = array ())
    {
        $this->shape = new TShape ($parent);
        $this->shape->parent = $parent;

        parent::__construct ($parent, $properties, array
        (
            $this->shape->self => array
            (
                'x' => 0,
                'y' => 0,
                'w' => 0,
                'h' => 1,

                'penColor'   => clLightBlue,
                'brushColor' => clLightBlue
            )
        ));
    }

    public function collusion ($position = array ())
    {
        if (class_exists ('resize'))
            resize::resize_object ($this->shape, $position);

        elseif (class_exists ('TResize'))
            TResize::Start ($this->shape, $position);

        else foreach ($position as $id => $pos)
            if ($id == 'x' || $id == 'y' || $id == 'w' || $id == 'h')
                $this->shape->$id = $pos;
    }
}

class FEdit extends FVisual
{
    public $edit;
    public $border;

    public function __construct ($parent, $properties = array ())
    {
        $this->edit = new TEdit ($parent);
        $this->edit->parent = $parent;

        $this->border = new FCollusion ($parent);
        $border = $this->border;

        parent::__construct ($parent, $properties, array
        (
            $this->edit->self => array
            (
                'autoSize'    => false,
                'borderStyle' => bsNone,

                'x' => 0,
                'y' => 0,
                'w' => 256,
                'h' => 18,

                'font' => array
                (
                    'name'  => 'Segoe UI Light',
                    'color' => clLight,
                    'size'  => 9
                ),

                'marginLeft'  => 4,
                'marginRight' => 4,
                'color'       => clDark2Gray,

                'text' => 'Edit',

                'onMouseEnter' => function ($self) use ($border)
                {
                    $self = _c($self);

                    $border->collusion (array (
                        'x' => $self->x,
                        'y' => $self->y + $self->h,
                        'w' => $self->w,
                        'h' => 1
                    ));
                },

                'onMouseLeave' => function ($self) use ($border)
                {
                    $self = _c($self);

                    $border->collusion (array (
                        'x' => $self->x + ($self->w / 2),
                        'y' => $self->y + $self->h,
                        'w' => 0,
                        'h' => 1
                    ));
                }
            ),

            $this->border->shape->self => array
            (
                'x' => 128,
                'y' => 18,
                'w' => 0,
                'h' => 1
            )
        ));
    }
}

class FMemo extends FVisual
{
    public $memo;
    public $border;

    public function __construct ($parent, $properties = array ())
    {
        $this->memo = new TMemo ($parent);
        $this->memo->parent = $parent;

        $this->border = new FCollusion ($parent);
        $border = $this->border;

        parent::__construct ($parent, $properties, array
        (
            $this->memo->self => array
            (
                'autoSize'    => false,
                'borderStyle' => bsNone,

                'x' => 0,
                'y' => 0,
                'w' => 312,
                'h' => 176,

                'font' => array
                (
                    'name'  => 'Segoe UI Light',
                    'color' => clLight,
                    'size'  => 10
                ),

                'scrollBars'  => ssVertical,
                'wantReturns' => true,
                'color'       => clDark2Gray,

                'text' => 'Memo',

                'onMouseEnter' => function ($self) use ($border)
                {
                    $self = _c($self);

                    $border->collusion (array (
                        'x' => $self->x,
                        'y' => $self->y + $self->h,
                        'w' => $self->w,
                        'h' => 1
                    ));
                },

                'onMouseLeave' => function ($self) use ($border)
                {
                    $self = _c($self);

                    $border->collusion (array (
                        'x' => $self->x + ($self->w / 2),
                        'y' => $self->y + $self->h,
                        'w' => 0,
                        'h' => 1
                    ));
                }
            ),

            $this->border->shape->self => array
            (
                'x' => 128,
                'y' => 18,
                'w' => 0,
                'h' => 1
            )
        ));
    }
}

class FEditor extends FVisual
{
    public $edit;

    public function __construct ($parent, $properties = array (), $syntax = false)
    {
        $this->edit = new TSynEdit ($parent);
        $this->edit->parent = $parent;

        gui_readStr ($this->edit->self,
            'object TSynEdit
                Gutter.Color = '. clDark2Gray .'
                Gutter.BorderColor = '. clLightBlue .'
                Gutter.LeftOffset = 6
                Gutter.RightOffset = 6
                Gutter.Font.Color = '. clLight .'
                Gutter.Font.Size = 10
            end'
        );

        parent::__construct ($parent, $properties, array
        (
            $this->edit->self => array
            (
                'autoSize'    => false,
                'borderStyle' => bsNone,

                'x' => 290,
                'y' => 16,
                'w' => 200,
                'h' => 400,

                'wordWrap'        => true,
                'showLineNumbers' => true,
			    'alwaysShowCaret' => true,

                'font' => array
                (
                    'color' => clLight
                ),

                'scrollBars'  => ssVertical,
                'wantReturns' => true,
                'color'       => clDark2Gray
            )
        ));

        if ($syntax)
		{
            if (!$this->edit->name)
                $this->edit->name = ('Editor__'. dechex ($this->fid, 10, 16));

            if (file_exists ($syntax))
                switch (fileExt ($syntax))
                {
                    case 'ini':
                        $ini = new TIniFileEx ($syntax);
                        
                        $colors = $ini->arr;
                    break;
                    
                    case 'json':
                        $colors = json_decode (file_get_contents ($syntax), true);
                    break;
                }

            else $syntaxPath = $syntax;

            if (!$syntaxPath)
                $syntaxPath = 'SynPHPSyn__'. sha1 (serialize ($colors));
            
            if (!is_a (c($parent->name .'->'. $syntaxPath), 'TSynPHPSyn'))
            {
                $hi = new TSynPHPSyn ($parent);
                $hi->name = $syntaxPath;
                $hi->loadFromArray ($colors);
            }
		
			gui_readstr ($this->edit->self,
				'object '. $this->edit->name .': TSynEdit
					Highlighter = '. $parent->name .'.'. $syntaxPath .'
				end'
            );
        }
    }
}

class FLabel extends FVisual
{
    public $label;

    public function __construct ($parent, $properties = array ())
    {
        $this->label = new TLabel ($parent);
        $this->label->parent = $parent;

        parent::__construct ($parent, $properties, array
        (
            $this->label->self => array
            (
                'autoSize' => false,

                'x' => 0,
                'y' => 0,
                'w' => 104,
                'h' => 24,

                'color'   => clDark2Gray,
                'caption' => 'Label',

                'font' => array
                (
                    'name'  => 'Segoe UI Light',
                    'color' => clLight,
                    'size'  => 10
                )
            )
        ));
    }
}

class FButton extends FVisual
{
    public $label;
    public $border;

    public function __construct ($parent, $properties = array ())
    {
        $this->label = new FLabel ($parent);
        $this->label->parent = $parent;

        $this->border = new TShape ($parent);
        $this->border->parent = $parent;

        parent::__construct ($parent, $properties, array
        (
            $this->label->label->self => array
            (
                'alignment'   => taCenter,
                'layout'      => tlCenter,
                'cursor'      => crHandPoint,
                'transparent' => false,

                'caption' => 'Button',

                'onMouseDown' => function ($self)
                {
                    _c($self)->color = clLightBlue;
                },

                'onMouseUp' => function ($self)
                {
                    _c($self)->color = clDark2Gray;
                }
            ),

            $this->border->self => array
            (
                'x' => 0,
                'y' => 24,
                'w' => 104,
                'h' => 1,

                'penColor'   => clLightBlue,
                'brushColor' => clLightBlue
            )
        ));
    }
}

class FQuickButton extends FVisual
{
    public $label;

    public function __construct ($parent, $properties = array ())
    {
        $this->label = new FLabel ($parent);
        $this->label->parent = $parent;

        parent::__construct ($parent, $properties, array
        (
            $this->label->label->self => array
            (
                'alignment'   => taCenter,
                'layout'      => tlCenter,
                'cursor'      => crHandPoint,
                'transparent' => false,

                'caption' => 'Button',

                'onMouseDown' => function ($self)
                {
                    _c($self)->color = clLightBlue;
                },

                'onMouseUp' => function ($self)
                {
                    _c($self)->color = clDark2Gray;
                }
            )
        ));
    }
}

class FInvertedButton extends FVisual
{
    public $label;
    public $border;

    public function __construct ($parent, $properties = array ())
    {
        $this->label = new FLabel ($parent);
        $this->label->parent = $parent;

        parent::__construct ($parent, $properties, array
        (
            $this->label->label->self => array
            (
                'alignment'   => taCenter,
                'layout'      => tlCenter,
                'cursor'      => crHandPoint,
                'transparent' => false,

                'color'   => clLightBlue,
                'caption' => 'Button',

                'onMouseDown' => function ($self)
                {
                    _c($self)->color = clDark2Gray;
                },

                'onMouseUp' => function ($self)
                {
                    _c($self)->color = clLightBlue;
                }
            )
        ));
    }
}

class FFillButton extends FVisual
{
    public $label;
    public $shape;
    public $border;

    public function __construct ($parent, $properties = array ())
    {
        $this->label = new FLabel ($parent);
        $this->label->parent = $parent;

        $this->shape = new TShape ($parent);
        $this->shape->parent = $parent;

        $this->border = new FCollusion ($parent);
        $collusion = $this->border;

        $this->label->label->toFront ();

        parent::__construct ($parent, $properties, array
        (
            $this->label->label->self => array
            (
                'alignment'   => taCenter,
                'layout'      => tlCenter,
                'cursor'      => crHandPoint,
                'transparent' => true,

                'color'   => clDark2Gray,
                'caption' => 'Button',

                'onMouseEnter' => function ($self) use ($collusion)
                {
                    $self = _c($self);

                    $collusion->collusion (array (
                        'x' => $self->x,
                        'y' => $self->y,
                        'w' => $self->w,
                        'h' => $self->h
                    ));
                },

                'onMouseLeave' => function ($self) use ($collusion)
                {
                    $self = _c($self);

                    $collusion->collusion (array (
                        'x' => $self->x,
                        'y' => $self->y + $self->h,
                        'w' => $self->w,
                        'h' => 1
                    ));
                }
            ),

            $this->shape->self => array
            (
                'x' => 0,
                'y' => 0,
                'w' => 104,
                'h' => 24,

                'penColor'   => clDark2Gray,
                'brushColor' => clDark2Gray
            ),

            $this->border->shape->self => array
            (
                'x' => 0,
                'y' => 24,
                'w' => 104,
                'h' => 1,
            )
        ));
    }
}

class FCrossCheckbox extends FVisual
{
    public $label;
    public $check;
    public $border;

    public function __construct ($parent, $properties = array ())
    {
        $this->label = new FLabel ($parent);
        $this->label->parent = $parent;

        $this->check = new TLabel ($parent);
        $this->check->parent = $parent;

        $this->border = new TShape ($parent);
        $this->border->parent = $parent;

        $this->check->toFront ();

        parent::__construct ($parent, $properties, array
        (
            $this->label->label->self => array
            (
                'x' => 24,
                'y' => 0,
                'w' => 104,
                'h' => 16,

                'layout'  => tlCenter,
                'caption' => 'Checkbox',

                'font' => array
                (
                    'size' => 9
                )
            ),

            $this->check->self => array
            (
                'autoSize' => false,

                'x' => 0,
                'y' => 0,
                'w' => 16,
                'h' => 16,

                'alignment'   => taCenter,
                'layout'      => tlCenter,
                'cursor'      => crHandPoint,

                'color'   => clDark2Gray,
                'caption' => '',

                'font' => array
                (
                    'name'  => 'Webdings',
                    'color' => clLightBlue,
                    'size'  => 10
                ),

                'onClick' => function ($self)
                {
                    $self = _c($self);

                    $self->caption = (($self->caption) ? '' : 'r');
                }
            ),

            $this->border->self => array
            (
                'x' => 0,
                'y' => 0,
                'w' => 16,
                'h' => 16,

                'penColor'   => clLightBlue,
                'brushColor' => clDark2Gray
            )
        ));
    }

    public function checked ($state = null)
    {
        if (is_bool ($state))
            $this->check->caption = ($state ? 'r' : '');

        else return ($this->check->caption == 'r');
    }
}

class FFillCheckbox extends FVisual
{
    public $label;
    public $border;

    public function __construct ($parent, $properties = array ())
    {
        $this->label = new FLabel ($parent);
        $this->label->parent = $parent;

        $this->border = new TShape ($parent);
        $this->border->parent = $parent;

        parent::__construct ($parent, $properties, array
        (
            $this->label->label->self => array
            (
                'x' => 24,
                'y' => 0,
                'w' => 104,
                'h' => 16,

                'layout'  => tlCenter,
                'caption' => 'Checkbox',

                'font' => array
                (
                    'size' => 9
                )
            ),

            $this->border->self => array
            (
                'x' => 0,
                'y' => 0,
                'w' => 16,
                'h' => 16,

                'cursor'     => crHandPoint,
                'penColor'   => clLightBlue,
                'brushColor' => clDark2Gray,

                'onMouseDown' => function ($self)
                {
                    $self = _c($self);

                    $self->brushColor = (($self->brushColor == clLightBlue) ? clDark2Gray : clLightBlue);
                }
            )
        ));
    }

    public function checked ($state = null)
    {
        if (is_bool ($state))
            $this->border->brushColor = ($state ? clLightBlue : clDark2Gray);

        else return ($this->border->brushColor == clLightBlue);
    }
}

class FProgress extends FVisual
{
    public $border;
    public $shape;

    protected $percents;

    public function __construct ($parent, $properties = array ())
    {
        $this->border = new TShape ($parent);
        $this->border->parent = $parent;

        $this->shape = new FCollusion ($parent);

        parent::__construct ($parent, $properties, array
        (
            $this->border->self => array
            (
                'x' => 0,
                'y' => 0,
                'w' => 256,
                'h' => 16,

                'penColor'   => clDark2Gray,
                'brushColor' => clDark2Gray
            ),

            $this->shape->shape->self => array
            (
                'x' => 0,
                'y' => 0,
                'w' => 0,
                'h' => 16,

                'penColor'   => clLightBlue,
                'brushColor' => clLightBlue
            )
        ));
    }

    public function progress ($progress = null)
    {
        $progressPart = $this->border->w / 100;
        $percents     = normilizeInt ((int) ($this->shape->shape->w / $progressPart));

        if ($this->percents && abs ($this->percents - $percents) < 2)
            $percents = $this->percents;

        if ($progress == null)
            return $percents;

        else
        {
            $progress = normilizeInt ($progress);

            $this->shape->collusion (array (
                'x' => $this->border->x,
                'y' => $this->border->y,
                'w' => normilizeInt ((int) $progress * $progressPart, 0, $this->border->w),
                'h' => $this->border->h
            ));

            $this->percents = $progress;
        }
    }
}

class FNotification extends FVisual
{
    public $shape;
    public $border;
    public $close;
    public $caption;
    public $text;

    public $openPosition;
    public $closePosition;

    public function __construct ($properties = array ())
    {
        $this->shape = new TForm;
        $this->shape->parent = $parent;

        $this->border = new TShape ($this->shape);
        $this->border->parent = $this->shape;

        $this->caption = new FLabel ($this->shape, array (
            'label' => array
            (
                'x' => 16,
                'y' => 8,
                'w' => 328,
                'h' => 32,

                'font' => array
                (
                    'size' => 20
                ),

                'caption' => 'Caption'
            )
        ));

        $this->text = new FLabel ($this->shape, array (
            'label' => array
            (
                'x' => 16,
                'y' => 48,
                'w' => 328,
                'h' => 56,

                'wordWrap' => true,

                'font' => array
                (
                    'size'  => 12,
                    'color' => clGray
                ),

                'caption' => 'Text'
            )
        ));

        $this->openPosition  = array ('x' => SCREEN_WIDTH - 392, 'y' => 8, 'w' => 392, 'h' => 112);
        $this->closePosition = array ('x' => SCREEN_WIDTH,       'y' => 8, 'w' => 392, 'h' => 112);

        parent::__construct (null, $properties, array
        (
            $this->shape->self => array
            (
                'x' => SCREEN_WIDTH,
                'y' => 8,
                'w' => 392,
                'h' => 112,

                'borderStyle' => bsNone,
                'color'       => clDarkGray
            ),

            $this->border->self => array
            (
                'w' => 4,
                'h' => 112,

                'brushColor'  => clLightBlue,
                'borderColor' => clLightBlue
            )
        ));

        $fid = $this->fid;
        $this->close = new FQuickButton ($this->shape, array (
            'label' => array
            (
                'x' => 360,
                'y' => 8,
                'w' => 24,
                'h' => 24,

                'font' => array
                (
                    'name' => 'Webdings'
                ),

                'color'   => clDarkGray,
                'layout'  => tlCenter,
                'caption' => 'r',

                'onClick' => function ($self) use ($fid)
                {
                    $object = getFainexObject ($fid);

                    $object->hide ();
                },

                'onMouseUp' => function ($self)
                {
                    _c($self)->color = clDarkGray;
                }
            )
        ));
    }

    public function show ($caption, $text)
    {
        $this->shape->show ();
        $this->shape->toFront ();

        $this->caption->caption = $caption;
        $this->text->caption    = $text;

        if (class_exists ('resize'))
            resize::resize_object ($this->shape, $this->openPosition);

        elseif (class_exists ('TResize'))
            TResize::Start ($this->shape, $this->openPosition);

        else foreach ($this->openPosition as $id => $pos)
            if ($id == 'x' || $id == 'y' || $id == 'w' || $id == 'h')
                $this->shape->$id = $pos;
    }

    public function hide ()
    {
        if (class_exists ('resize'))
            resize::resize_object ($this->shape, $this->closePosition);

        elseif (class_exists ('TResize'))
            TResize::Start ($this->shape, $this->closePosition);

        else foreach ($this->closePosition as $id => $pos)
            if ($id == 'x' || $id == 'y' || $id == 'w' || $id == 'h')
                $this->shape->$id = $pos;
    }
}

class FVerticalList extends FVisual
{
    public $listBox;
    public $items;

    public function __construct ($parent, $items = array (), $properties = array ())
    {
        $this->listBox = new TScrollBox ($parent);
        $this->listBox->parent = $parent;

        parent::__construct ($parent, $properties, array
        (
            $this->listBox->self => array
            (
                'x' => 0,
                'y' => 0,
                'w' => 200,
                'h' => 312,

                'borderStyle' => bsNone,
                'transparent' => false,
                'color'       => clDarkGray
            )
        ));

        self::loadItemsList ($items);  
    }

    public function loadItemsList ($items, $y = 0)
    {
        $w = $this->listBox->w - 1;

        if (sizeof ($items) * 24 > $this->listBox->h)
            $w -= 21;

        foreach ($items as $name => $func)
        {
            if (is_string ($func) && is_int ($name))
                $name = $func;

            $this->items[$name] = new FButton ($this->listBox, array (
                'label' => array
                (
                    'x' => 1,
                    'y' => $y,
                    'w' => $w,
                    'h' => 24,

                    'caption' => "  $name"
                ),

                'border' => array
                (
                    'x' => 0,
                    'y' => $y,
                    'w' => 1,
                    'h' => 24
                )
            ));

            if (is_callable ($func))
                $this->items[$name]->setProperties (array (
                    'label' => array ('onClick' => $func)
                ));

            $y += 24;
        }

        return $y;
    }
}

class FHorizontalList extends FVisual
{
    public $listBox;
    public $items;

    public function __construct ($parent, $items = array (), $properties = array ())
    {
        $this->listBox = new TScrollBox ($parent);
        $this->listBox->parent = $parent;

        parent::__construct ($parent, $properties, array
        (
            $this->listBox->self => array
            (
                'x' => 0,
                'y' => 0,
                'w' => 200,
                'h' => 26,

                'borderStyle' => bsNone,
                'transparent' => false,
                'color'       => clDarkGray
            )
        ));

        self::loadItemsList ($items);  
    }

    public function loadItemsList ($items, $x = 0)
    {
        if (($size = sizeof ($items) * 104) > $this->listBox->w)
            $this->listBox->w = $size;

        foreach ($items as $name => $func)
        {
            if (is_string ($func) && is_int ($name))
                $name = $func;

            $this->items[$name] = new FButton ($this->listBox, array (
                'label' => array
                (
                    'x' => $x,
                    'y' => 0,
                    'w' => 104,
                    'h' => $this->listBox->h,

                    'caption' => "  $name"
                ),

                'border' => array
                (
                    'x' => $x,
                    'y' => $this->listBox->h - 2,
                    'w' => 104,
                    'h' => 2
                )
            ));

            if (is_callable ($func))
                $this->items[$name]->setProperties (array (
                    'label' => array ('onClick' => $func)
                ));

            $x += 104;
        }

        return $x;
    }
}

class FMenu extends FVisual
{
    public $menuBox;
    public $items;

    public function __construct ($parent, $items = array (), $properties = array ())
    {
        $this->menuBox = new FHorizontalList ($parent, array (), array (
            'listBox' => array
            (
                'w' => sizeof ($items) * 104
            )
        ));

        parent::__construct ($parent, $properties);

        self::loadItems ($items, $this->menuBox);  
    }

    protected function loadItems ($items, $parent, $deep = 0)
    {
        $this->items[$deep][] = $parent;

        $y   = 0;
        $fid = $this->fid;

        foreach ($items as $name => $subItems)
            if (is_array ($subItems))
            {
                $box = sizeof ($this->items[$deep + 1]);

                $y = $parent->loadItemsList (array ($name => function ($self) use ($deep, $box, $fid)
                {
                    $self = _c($self);

                    if ($self->hint != '~')
                    {
                        $self->onMouseDown = function ($self) {};
                        $self->onMouseUp   = function ($self) {};

                        $self->hint     = '~';
                        $self->showHint = false;

                        $self->color = clDark2Gray;
                    }
                    
                    $menu = getFainexObject ($fid);;

                    if ($self->color == clDark2Gray)
                    {
                        $self->color = clLightBlue;

                        $menu->items[$deep + 1][$box]->listBox->visible = true;
                    }

                    else
                    {
                        $self->color = clDark2Gray;

                        while (isset ($menu->items[++$deep]))
                            foreach ($menu->items[$deep] as $id => $item)
                            {
                                $item->listBox->visible = false;

                                foreach ($item->items as $iid => $nitems)
                                    $nitems->label->color = clDark2Gray;
                            }
                    }
                }), $y);

                if ($deep == 0)
                    $params = array
                    (
                        'x' => $parent->listBox->x + $y - 104,
                        'y' => $parent->listBox->y + $parent->listBox->h,
                        'w' => 200,
                        'h' => sizeof ($subItems) * 24
                    );

                else $params = array
                (
                    'x' => $parent->listBox->x + $parent->listBox->w,
                    'y' => $parent->listBox->y + $y - 24,
                    'w' => 104,
                    'h' => sizeof ($subItems) * 24
                );

                $newParent = new FVerticalList ($this->menuBox->listBox->parent, array (), array (
                    'listBox' => $params
                ));

                $newParent->listBox->visible = false;

                self::loadItems ($subItems, $newParent, $deep + 1);
            }

            else
            {
                if (is_string ($subItems) && is_int ($name))
                    $name = $subItems;

                $y = $parent->loadItemsList (array ($name => function ($self) use ($fid, $subItems)
                {
                    if (is_callable ($subItems))
                        call_user_func ($subItems, $self);

                    $menu = getFainexObject ($fid);
                    $deep = -1;

                    while (isset ($menu->items[++$deep]))
                        foreach ($menu->items[$deep] as $id => $item)
                        {
                            if ($deep > 0)
                                $item->listBox->visible = false;

                            foreach ($item->items as $iid => $nitems)
                                $nitems->label->color = clDark2Gray;
                        }
                }), $y);
            }
    }
}

class FHeadMenu extends FVisual
{
    public $caption;
    public $close;
    public $hide;
    public $menu;

    public function __construct ($parent, $items = array (), $properties = array (), $widthExp = 16)
    {
        $this->caption = new FLabel ($parent);
        $this->caption->anchors = 'akLeft,akRight,akTop';

        $this->close = new FQuickButton ($parent, array (
            'label' => array
            (
                'x' => $parent->w - 24 - $widthExp,
                'y' => $this->caption->label->y,
                'w' => 24,
                'h' => 24,

                'anchors' => 'akRight,akTop',

                'font' => array
                (
                    'name' => 'Webdings'
                ),

                'caption' => 'r',

                'onClick' => function ($self)
                {
					if(function_exists("closeUSM")    ){
					closeUSM();
					}else{
                    application_terminate ();
					}
                }
            )
        ));

        $this->hide = new FQuickButton ($parent, array (
            'label' => array
            (
                'x' => $parent->w - 48 - $widthExp,
                'y' => $this->caption->label->y,
                'w' => 24,
                'h' => 24,

                'anchors' => 'akRight,akTop',

                'layout'  => tlTop,
                'caption' => '_',

                'onClick' => function ($self)
                {
                    application_minimize ();
                }
            )
        ));

        if (is_array ($items) && sizeof ($items) > 0)
            $this->menu = new FMenu ($parent, $items, array (
                'menuBox' => array
                (
                    'y' => $this->caption->label->y + 24,
                    'w' => $parent->w - $widthExp,
                    'h' => 20,

                    'anchors' => 'akLeft,akRight,akTop',

                    'color' => clDark2Gray
                )
            ));

        parent::__construct ($parent, $properties, array (
            'caption' => array
            (
                'transparent' => false,

                'w' => $parent->w - $widthExp,
                'h' => 24,

                'alignment' => taCenter,
                'layout'    => tlCenter,
                'caption'   => 'Form Caption',

                'onMouseDown' => function ($self, $button, $shift, $x, $y)
                {
                    $GLOBALS["__position_$self"] = array ($x, $y);
                },

                'onMouseUp' => function ($self)
                {
                    unset ($GLOBALS["__position_$self"]);
                },

                'onMouseMove' => function ($self, $shift, $x, $y)
                {
                    if (isset ($GLOBALS["__position_$self"]))
                    {
                        $form = _c($self)->parent;

                        $form->x += $x - $GLOBALS["__position_$self"][0];
                        $form->y += $y - $GLOBALS["__position_$self"][1];
                    }
                }
            )
        ));
    }
}

class FForm extends FVisual
{
    public $head;

    public function __construct ($parent, $items = array (), $properties = array (), $widthExp = 0)
    {
        $this->head = new FHeadMenu ($parent, $items, $properties, $widthExp);
        $this->head->caption->caption = $parent->caption;

        parent::__construct ($parent, $properties, array (
            $parent->self => array
            (
                'borderStyle'    => bsNone,
                'doubleBuffered' => true,
                'screenSnap'     => true,

                'alphaBlend'      => true,
                'alphaBlendValue' => 250,

                'color' => clBackgroundGray
            )
        ));
    }
}

class FVK extends FVisual
{
    public $appID;
    public $apiVersion;
    public $token;

    public function __construct ()
    {
        parent::__construct ();
    }

    public function openAuthDialog ($access = 'notify,friends,photos,audio,video,stories,pages,status,notes,messages,wall,offline,docs,groups,email', $onAuthFunction = null) // Для работы необходимы библиотеки Chromium'а (компонент TChromium на проекте)
    {
        $form = new TForm;
        $form->w = 680;
        $form->h = 400;

        $form->caption     = 'VK Auth Dialog';
        $form->borderStyle = bsToolWindow;

        $browser = new TChromium ($form);
        $browser->parent = $form;
        $browser->align  = alClient;

        $fid = $this->fid;
        $browser->onAddresschange = function ($self, $url) use ($form, $fid, $onAuthFunction)
        {
            if ($pos = strpos ($url, '#access_token='))
            {
                $vk = getFainexObject ($fid);
                $vk->token = substr ($url, $pos + 14, strpos ($url, '&', $pos + 14) - $pos - 14);

                $form->close ();

                if (is_callable ($onAuthFunction))
                    call_user_func ($onAuthFunction, $vk->token);
            }
        };

        $form->show ();

        $browser->url = 'http://oauth.vk.com/authorize?client_id='. $this->appID .'&display=page&redirect_uri=http://oauth.vk.com/blank.html&scope='. $access .'&response_type=token&v='. $this->apiVersion;
    }

    public function request ($method, $params = array (), $end = '')
    {
        return get_request ('https://api.vk.com/method/'. $method, $params, '&access_token='. $this->token .'&v='. $this->apiVersion .$end);
    }
}

?>
