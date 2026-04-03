<?php
declare(strict_types=1);

namespace FCW\Services;

final class AssessmentCatalog
{
    /**
     * @return array<int, array{title:string,questions:array<int, array{0:string,1:string}>}>
     */
    public static function sections(): array
    {
        return [
            [
                'title' => '1) Female-Specific Risk & History',
                'questions' => [
                    ['Have you ever been diagnosed with an autoimmune disease?', 'q1_1'],
                    ['Did your symptoms begin after puberty?', 'q1_2'],
                    ['Did your symptoms begin post-pregnancy / postpartum?', 'q1_3'],
                    ['Did your symptoms begin after miscarriage or abortion?', 'q1_4'],
                    ['Did your symptoms begin during perimenopause / menopause?', 'q1_5'],
                    ['Do autoimmune or thyroid conditions run in your family?', 'q1_6'],
                    ['Have you experienced symptom worsening around your menstrual cycle?', 'q1_7'],
                ],
            ],
            [
                'title' => '2) Menstrual & Reproductive Health',
                'questions' => [
                    ['Are your menstrual cycles irregular?', 'q2_1'],
                    ['Are your menstrual cycles painful?', 'q2_2'],
                    ['Do you experience severe PMS or PMDD?', 'q2_3'],
                    ['Do you experience heavy or prolonged bleeding?', 'q2_4'],
                    ['Do you experience missed periods?', 'q2_5'],
                    ['Have you been diagnosed with PCOS or Endometriosis?', 'q2_6'],
                    ['History of infertility or recurrent pregnancy loss?', 'q2_7'],
                ],
            ],
            [
                'title' => '3) Hormonal & Metabolic Clues',
                'questions' => [
                    ['Do you experience unexplained weight gain or weight-loss resistance?', 'q3_1'],
                    ['Cold intolerance or heat intolerance?', 'q3_2'],
                    ['Hair thinning or hair loss?', 'q3_3'],
                    ['Low libido?', 'q3_4'],
                    ['Mood changes related to your cycle?', 'q3_5'],
                    ['History of thyroid dysfunction or postpartum thyroiditis?', 'q3_6'],
                ],
            ],
            [
                'title' => '4) Energy, Fatigue & Sleep',
                'questions' => [
                    ['Do you feel chronically fatigued despite adequate rest?', 'q4_1'],
                    ['Do you wake up feeling unrefreshed?', 'q4_2'],
                    ['Do you experience energy crashes during the day?', 'q4_3'],
                    ['Difficulty falling asleep or staying asleep?', 'q4_4'],
                ],
            ],
            [
                'title' => '5) Inflammation & Pain Patterns',
                'questions' => [
                    ['Do you experience joint pain or stiffness?', 'q5_1'],
                    ['Morning stiffness lasting >30 minutes?', 'q5_2'],
                    ['Migratory pain (moves from joint to joint)?', 'q5_3'],
                    ['Do symptoms fluctuate in flares and remissions?', 'q5_4'],
                ],
            ],
            [
                'title' => '6) Gut & Immune Health',
                'questions' => [
                    ['Do you experience bloating, gas, or abdominal pain?', 'q6_1'],
                    ['Constipation or diarrhea?', 'q6_2'],
                    ['Food sensitivities?', 'q6_3'],
                    ['History of IBS, IBD, or celiac disease?', 'q6_4'],
                    ['Frequent antibiotic use?', 'q6_5'],
                    ['Do symptoms worsen after infections?', 'q6_6'],
                ],
            ],
            [
                'title' => '7) Skin, Hair & External Clues',
                'questions' => [
                    ['Dry skin?', 'q7_1'],
                    ['Rashes or photosensitivity?', 'q7_2'],
                    ['Eczema or psoriasis?', 'q7_3'],
                    ['Brittle nails or slow nail growth?', 'q7_4'],
                ],
            ],
            [
                'title' => '8) Neurological & Mood Symptoms',
                'questions' => [
                    ['Brain fog or memory issues?', 'q8_1'],
                    ['Anxiety or depression without clear cause?', 'q8_2'],
                    ['Tingling, numbness, or burning sensations?', 'q8_3'],
                    ['Headaches or migraines?', 'q8_4'],
                ],
            ],
            [
                'title' => '9) Stress, Trauma & Lifestyle Triggers',
                'questions' => [
                    ['Chronic emotional stress?', 'q9_1'],
                    ['Major life trauma?', 'q9_2'],
                    ['Sleep duration <7 hours regularly?', 'q9_3'],
                    ['High stress levels?', 'q9_4'],
                ],
            ],
            [
                'title' => '10) Autoimmune Red Flags',
                'questions' => [
                    ['Unexplained fevers?', 'q10_1'],
                    ['Rapid unexplained weight loss?', 'q10_2'],
                    ['Persistent swelling?', 'q10_3'],
                    ['Vision changes?', 'q10_4'],
                    ['Blood in stool or urine?', 'q10_5'],
                ],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function questionMap(): array
    {
        return [
            'q1_1' => 'diagnosed_autoimmune',
            'q1_2' => 'symptoms_after_puberty',
            'q1_3' => 'symptoms_post_pregnancy',
            'q1_4' => 'symptoms_after_miscarriage',
            'q1_5' => 'symptoms_during_menopause',
            'q1_6' => 'family_history_autoimmune',
            'q1_7' => 'symptoms_worse_menstrual_cycle',
            'q2_1' => 'irregular_cycles',
            'q2_2' => 'painful_cycles',
            'q2_3' => 'severe_pms',
            'q2_4' => 'heavy_bleeding',
            'q2_5' => 'missed_periods',
            'q2_6' => 'pcos_or_endometriosis',
            'q2_7' => 'infertility_history',
            'q3_1' => 'unexplained_weight_changes',
            'q3_2' => 'temperature_intolerance',
            'q3_3' => 'hair_loss',
            'q3_4' => 'low_libido',
            'q3_5' => 'mood_cycle_changes',
            'q3_6' => 'thyroid_history',
            'q4_1' => 'chronic_fatigue',
            'q4_2' => 'unrefreshed_sleep',
            'q4_3' => 'energy_crashes',
            'q4_4' => 'sleep_difficulty',
            'q5_1' => 'joint_pain',
            'q5_2' => 'morning_stiffness',
            'q5_3' => 'migratory_pain',
            'q5_4' => 'flare_remission',
            'q6_1' => 'bloating',
            'q6_2' => 'bowel_issues',
            'q6_3' => 'food_sensitivity',
            'q6_4' => 'gut_disease_history',
            'q6_5' => 'frequent_antibiotics',
            'q6_6' => 'post_infection_worsening',
            'q7_1' => 'dry_skin',
            'q7_2' => 'rashes',
            'q7_3' => 'eczema',
            'q7_4' => 'brittle_nails',
            'q8_1' => 'brain_fog',
            'q8_2' => 'anxiety_depression',
            'q8_3' => 'tingling_numbness',
            'q8_4' => 'headaches',
            'q9_1' => 'chronic_stress',
            'q9_2' => 'major_trauma',
            'q9_3' => 'short_sleep',
            'q9_4' => 'high_stress',
            'q10_1' => 'unexplained_fever',
            'q10_2' => 'rapid_weight_loss',
            'q10_3' => 'persistent_swelling',
            'q10_4' => 'vision_changes',
            'q10_5' => 'blood_in_stool_urine',
        ];
    }

    /**
     * @return list<string>
     */
    public static function booleanFields(): array
    {
        return array_values(self::questionMap());
    }

    /**
     * @return array<string, string>
     */
    public static function fieldLabels(): array
    {
        return [
            'diagnosed_autoimmune' => 'Diagnosed autoimmune disease',
            'symptoms_after_puberty' => 'Symptoms after puberty',
            'symptoms_post_pregnancy' => 'Symptoms post pregnancy',
            'symptoms_after_miscarriage' => 'Symptoms after miscarriage',
            'symptoms_during_menopause' => 'Symptoms during menopause',
            'family_history_autoimmune' => 'Family history autoimmune',
            'symptoms_worse_menstrual_cycle' => 'Symptoms worse during cycle',
            'irregular_cycles' => 'Irregular cycles',
            'painful_cycles' => 'Painful cycles',
            'severe_pms' => 'Severe PMS',
            'heavy_bleeding' => 'Heavy bleeding',
            'missed_periods' => 'Missed periods',
            'pcos_or_endometriosis' => 'PCOS / Endometriosis',
            'infertility_history' => 'Infertility history',
            'unexplained_weight_changes' => 'Unexplained weight changes',
            'temperature_intolerance' => 'Temperature intolerance',
            'hair_loss' => 'Hair loss',
            'low_libido' => 'Low libido',
            'mood_cycle_changes' => 'Mood cycle changes',
            'thyroid_history' => 'Thyroid history',
            'chronic_fatigue' => 'Chronic fatigue',
            'unrefreshed_sleep' => 'Unrefreshed sleep',
            'energy_crashes' => 'Energy crashes',
            'sleep_difficulty' => 'Sleep difficulty',
            'joint_pain' => 'Joint pain',
            'morning_stiffness' => 'Morning stiffness',
            'migratory_pain' => 'Migratory pain',
            'flare_remission' => 'Flare remission',
            'bloating' => 'Bloating',
            'bowel_issues' => 'Bowel issues',
            'food_sensitivity' => 'Food sensitivity',
            'gut_disease_history' => 'Gut disease history',
            'frequent_antibiotics' => 'Frequent antibiotics',
            'post_infection_worsening' => 'Post infection worsening',
            'dry_skin' => 'Dry skin',
            'rashes' => 'Rashes',
            'eczema' => 'Eczema',
            'brittle_nails' => 'Brittle nails',
            'brain_fog' => 'Brain fog',
            'anxiety_depression' => 'Anxiety / depression',
            'tingling_numbness' => 'Tingling / numbness',
            'headaches' => 'Headaches',
            'chronic_stress' => 'Chronic stress',
            'major_trauma' => 'Major trauma',
            'short_sleep' => 'Short sleep',
            'high_stress' => 'High stress',
            'unexplained_fever' => 'Unexplained fever',
            'rapid_weight_loss' => 'Rapid weight loss',
            'persistent_swelling' => 'Persistent swelling',
            'vision_changes' => 'Vision changes',
            'blood_in_stool_urine' => 'Blood in stool / urine',
        ];
    }
}
