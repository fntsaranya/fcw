<?php
declare(strict_types=1);

namespace FCW\Services;

final class AssessmentScoringService
{
    /**
     * @return array{fieldValues:array<string,bool>,score:int,interpretation:string}
     */
    public static function fromQuestionInput(array $input): array
    {
        $questionMap = AssessmentCatalog::questionMap();
        $fieldValues = [];
        $score = 0;

        foreach ($questionMap as $questionId => $fieldName) {
            $answer = strtolower(trim((string) ($input[$questionId] ?? 'no')));
            $isYes = $answer === 'yes';
            $fieldValues[$fieldName] = $isYes;

            if ($isYes) {
                $score++;
            }
        }

        return [
            'fieldValues' => $fieldValues,
            'score' => $score,
            'interpretation' => self::interpret($score),
        ];
    }

    /**
     * @return array{fieldValues:array<string,bool>,score:int,interpretation:string}
     */
    public static function fromCheckboxInput(array $input): array
    {
        $fieldValues = [];
        $score = 0;

        foreach (AssessmentCatalog::booleanFields() as $fieldName) {
            $isChecked = array_key_exists($fieldName, $input);
            $fieldValues[$fieldName] = $isChecked;
            if ($isChecked) {
                $score++;
            }
        }

        return [
            'fieldValues' => $fieldValues,
            'score' => $score,
            'interpretation' => self::interpret($score),
        ];
    }

    public static function interpret(int $score): string
    {
        if ($score >= 13) {
            return 'High suspicion of autoimmune involvement';
        }

        if ($score >= 6) {
            return 'Moderate immune dysregulation';
        }

        return 'Low suspicion';
    }
}
