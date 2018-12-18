<?php

namespace Database\Generators\Parts;

require_once __DIR__ . '/../Definitions.php';

use const Database\Generators\EOL;
use const Database\Generators\TAB;

use Database\Generators\GeneratedPart;

class ClassGeneratedPart extends GeneratedPart
{

    /**
     * @return string
     */
    public function process(): string
    {
        $output = "use Database\IDatabaseConnection;" . EOL . EOL;
        $output .= "class {$this->className}" . EOL;
        $output .= "{";

        $parts = [
            new ConstantsGeneratedPart(),
            new FieldsGeneratedPart(),
            new ConstructorGeneratedPart(),
            new PropertiesGeneratedPart(),
            new MethodAllGeneratedPart(),
            new MethodFindByPKeyGeneratedPart(),
            new MethodFromArrayGeneratedPart(),
            new MethodAddGeneratedPart(),
            new MethodAsArrayGeneratedPart(),
            new MethodDeleteGeneratedPart(),
            new MethodUpdateGeneratedPart()
        ];

        foreach ($parts as $part) {
            /** @var GeneratedPart $part */
            $part->init($this->connection, $this->dbName, $this->tableName, $this->tableColumns, $this->className);
            $sectionResult = $part->process();

            if (strlen($sectionResult) == 0)
                continue;

            $output .= EOL . $sectionResult . EOL;
        }

        return $output . "}";
    }
}