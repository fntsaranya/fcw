<?php
declare(strict_types=1);

namespace FCW\Repositories;

use FCW\Core\Database;
use PDO;

final class PatientIntakeRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    public function create(
        int $appointmentId,
        string $patientName,
        ?string $formDate,
        ?int $age,
        ?string $sex,
        ?string $address,
        ?string $surroundingArea,
        ?string $occupation,
        ?string $dietaryPreference,
        ?string $covidVaccination,
        ?string $weight,
        ?string $height,
        ?string $bmi,
        ?string $hipCircumference,
        ?string $waistCircumference,
        ?string $foodSensitivities,
        ?string $foodAllergies,
        ?string $craveBinge,
        ?string $dietFollowed,
        ?string $otherSensitivities,
        ?string $toxicities,
        bool $sleepRegular,
        ?string $sleepDetails,
        ?string $fmMother,
        ?string $fmFather,
        ?string $fmSister,
        ?string $fmBrother,
        ?string $fmGrandparents,
        ?string $fmSpouse,
        ?string $fmChildren,
        ?string $currentConcern,
        ?array $healthIssues,
        ?array $timelineHistory,
        ?array $specificQuestions,
        ?array $medications
    ): void {
        $stmt = $this->pdo->prepare('
            INSERT INTO patient_intake_forms (
                appointment_id, patient_name, form_date, age, sex, 
                address, surrounding_area, occupation, dietary_preference, covid_vaccination,
                weight, height, bmi, hip_circumference, waist_circumference, food_sensitivities, food_allergies, crave_binge, diet_followed,
                other_sensitivities, toxicities, 
                sleep_regular, sleep_details, fm_mother, fm_father, fm_sister, fm_brother,
                fm_grandparents, fm_spouse, fm_children, current_concern,
                health_issues_json, timeline_history_json, specific_questions_json, medications_json
            ) VALUES (
                :appointment_id, :patient_name, :form_date, :age, :sex, 
                :address, :surrounding_area, :occupation, :dietary_preference, :covid_vaccination,
                :weight, :height, :bmi, :hip_circumference, :waist_circumference, :food_sensitivities, :food_allergies, :crave_binge, :diet_followed,
                :other_sensitivities, :toxicities, 
                :sleep_regular, :sleep_details, :fm_mother, :fm_father, :fm_sister, :fm_brother,
                :fm_grandparents, :fm_spouse, :fm_children, :current_concern,
                :health_issues_json, :timeline_history_json, :specific_questions_json, :medications_json
            )
        ');

        $driver = Database::driver();
        $boolType = $driver === 'postgres' ? PDO::PARAM_BOOL : PDO::PARAM_INT;
        $sleepRegularVal = $driver === 'postgres' ? $sleepRegular : ($sleepRegular ? 1 : 0);

        $stmt->bindValue(':appointment_id', $appointmentId, PDO::PARAM_INT);
        $stmt->bindValue(':patient_name', $patientName);
        $stmt->bindValue(':form_date', $formDate);
        $stmt->bindValue(':age', $age, PDO::PARAM_INT);
        $stmt->bindValue(':sex', $sex);
        $stmt->bindValue(':address', $address);
        $stmt->bindValue(':surrounding_area', $surroundingArea);
        $stmt->bindValue(':occupation', $occupation);
        $stmt->bindValue(':dietary_preference', $dietaryPreference);
        $stmt->bindValue(':covid_vaccination', $covidVaccination);
        $stmt->bindValue(':weight', $weight);
        $stmt->bindValue(':height', $height);
        $stmt->bindValue(':bmi', $bmi);
        $stmt->bindValue(':hip_circumference', $hipCircumference);
        $stmt->bindValue(':waist_circumference', $waistCircumference);
        $stmt->bindValue(':food_sensitivities', $foodSensitivities);
        $stmt->bindValue(':food_allergies', $foodAllergies);
        $stmt->bindValue(':crave_binge', $craveBinge);
        $stmt->bindValue(':diet_followed', $dietFollowed);
        $stmt->bindValue(':other_sensitivities', $otherSensitivities);
        $stmt->bindValue(':toxicities', $toxicities);
        $stmt->bindValue(':sleep_regular', $sleepRegularVal, $boolType);
        $stmt->bindValue(':sleep_details', $sleepDetails);
        $stmt->bindValue(':fm_mother', $fmMother);
        $stmt->bindValue(':fm_father', $fmFather);
        $stmt->bindValue(':fm_sister', $fmSister);
        $stmt->bindValue(':fm_brother', $fmBrother);
        $stmt->bindValue(':fm_grandparents', $fmGrandparents);
        $stmt->bindValue(':fm_spouse', $fmSpouse);
        $stmt->bindValue(':fm_children', $fmChildren);
        $stmt->bindValue(':current_concern', $currentConcern);
        
        $stmt->bindValue(':health_issues_json', $healthIssues ? json_encode($healthIssues) : null);
        $stmt->bindValue(':timeline_history_json', $timelineHistory ? json_encode($timelineHistory) : null);
        $stmt->bindValue(':specific_questions_json', $specificQuestions ? json_encode($specificQuestions) : null);
        $stmt->bindValue(':medications_json', $medications ? json_encode($medications) : null);

        $stmt->execute();
    }

    public function update(
        int $appointmentId,
        string $patientName,
        ?string $formDate,
        ?int $age,
        ?string $sex,
        ?string $address,
        ?string $surroundingArea,
        ?string $occupation,
        ?string $dietaryPreference,
        ?string $covidVaccination,
        ?string $weight,
        ?string $height,
        ?string $bmi,
        ?string $hipCircumference,
        ?string $waistCircumference,
        ?string $foodSensitivities,
        ?string $foodAllergies,
        ?string $craveBinge,
        ?string $dietFollowed,
        ?string $otherSensitivities,
        ?string $toxicities,
        bool $sleepRegular,
        ?string $sleepDetails,
        ?string $fmMother,
        ?string $fmFather,
        ?string $fmSister,
        ?string $fmBrother,
        ?string $fmGrandparents,
        ?string $fmSpouse,
        ?string $fmChildren,
        ?string $currentConcern,
        ?array $healthIssues,
        ?array $timelineHistory,
        ?array $specificQuestions,
        ?array $medications
    ): void {
        $driver = Database::driver();
        $boolType = $driver === 'postgres' ? PDO::PARAM_BOOL : PDO::PARAM_INT;
        $sleepRegularVal = $driver === 'postgres' ? $sleepRegular : ($sleepRegular ? 1 : 0);

        $stmt = $this->pdo->prepare('
            UPDATE patient_intake_forms SET
                patient_name = :patient_name, form_date = :form_date, age = :age, sex = :sex, 
                address = :address, surrounding_area = :surrounding_area, occupation = :occupation, dietary_preference = :dietary_preference, covid_vaccination = :covid_vaccination,
                weight = :weight, height = :height, bmi = :bmi, hip_circumference = :hip_circumference, waist_circumference = :waist_circumference, food_sensitivities = :food_sensitivities, food_allergies = :food_allergies, crave_binge = :crave_binge, diet_followed = :diet_followed,
                other_sensitivities = :other_sensitivities, toxicities = :toxicities, 
                sleep_regular = :sleep_regular, sleep_details = :sleep_details, 
                fm_mother = :fm_mother, fm_father = :fm_father, fm_sister = :fm_sister, fm_brother = :fm_brother,
                fm_grandparents = :fm_grandparents, fm_spouse = :fm_spouse, fm_children = :fm_children,
                current_concern = :current_concern, 
                health_issues_json = :health_issues_json, timeline_history_json = :timeline_history_json, 
                specific_questions_json = :specific_questions_json, medications_json = :medications_json
            WHERE appointment_id = :appointment_id
        ');

        $stmt->bindValue(':appointment_id', $appointmentId, PDO::PARAM_INT);
        $stmt->bindValue(':patient_name', $patientName);
        $stmt->bindValue(':form_date', $formDate);
        $stmt->bindValue(':age', $age, PDO::PARAM_INT);
        $stmt->bindValue(':sex', $sex);
        $stmt->bindValue(':address', $address);
        $stmt->bindValue(':surrounding_area', $surroundingArea);
        $stmt->bindValue(':occupation', $occupation);
        $stmt->bindValue(':dietary_preference', $dietaryPreference);
        $stmt->bindValue(':covid_vaccination', $covidVaccination);
        $stmt->bindValue(':weight', $weight);
        $stmt->bindValue(':height', $height);
        $stmt->bindValue(':bmi', $bmi);
        $stmt->bindValue(':hip_circumference', $hipCircumference);
        $stmt->bindValue(':waist_circumference', $waistCircumference);
        $stmt->bindValue(':food_sensitivities', $foodSensitivities);
        $stmt->bindValue(':food_allergies', $foodAllergies);
        $stmt->bindValue(':crave_binge', $craveBinge);
        $stmt->bindValue(':diet_followed', $dietFollowed);
        $stmt->bindValue(':other_sensitivities', $otherSensitivities);
        $stmt->bindValue(':toxicities', $toxicities);
        $stmt->bindValue(':sleep_regular', $sleepRegularVal, $boolType);
        $stmt->bindValue(':sleep_details', $sleepDetails);
        $stmt->bindValue(':fm_mother', $fmMother);
        $stmt->bindValue(':fm_father', $fmFather);
        $stmt->bindValue(':fm_sister', $fmSister);
        $stmt->bindValue(':fm_brother', $fmBrother);
        $stmt->bindValue(':fm_grandparents', $fmGrandparents);
        $stmt->bindValue(':fm_spouse', $fmSpouse);
        $stmt->bindValue(':fm_children', $fmChildren);
        $stmt->bindValue(':current_concern', $currentConcern);
        $stmt->bindValue(':health_issues_json', $healthIssues ? json_encode($healthIssues) : null);
        $stmt->bindValue(':timeline_history_json', $timelineHistory ? json_encode($timelineHistory) : null);
        $stmt->bindValue(':specific_questions_json', $specificQuestions ? json_encode($specificQuestions) : null);
        $stmt->bindValue(':medications_json', $medications ? json_encode($medications) : null);

        $stmt->execute();
    }

    public function upsert(
        int $appointmentId,
        string $patientName,
        ?string $formDate,
        ?int $age,
        ?string $sex,
        ?string $address,
        ?string $surroundingArea,
        ?string $occupation,
        ?string $dietaryPreference,
        ?string $covidVaccination,
        ?string $weight,
        ?string $height,
        ?string $bmi,
        ?string $hipCircumference,
        ?string $waistCircumference,
        ?string $foodSensitivities,
        ?string $foodAllergies,
        ?string $craveBinge,
        ?string $dietFollowed,
        ?string $otherSensitivities,
        ?string $toxicities,
        bool $sleepRegular,
        ?string $sleepDetails,
        ?string $fmMother,
        ?string $fmFather,
        ?string $fmSister,
        ?string $fmBrother,
        ?string $fmGrandparents,
        ?string $fmSpouse,
        ?string $fmChildren,
        ?string $currentConcern,
        ?array $healthIssues,
        ?array $timelineHistory,
        ?array $specificQuestions,
        ?array $medications
    ): void {
        if ($this->findByAppointmentId($appointmentId)) {
            $this->update(
                $appointmentId, $patientName, $formDate, $age, $sex, $address, $surroundingArea, $occupation,
                $dietaryPreference, $covidVaccination, $weight, $height, $bmi, $hipCircumference, $waistCircumference,
                $foodSensitivities, $foodAllergies, $craveBinge, $dietFollowed, $otherSensitivities, $toxicities,
                $sleepRegular, $sleepDetails, $fmMother, $fmFather, $fmSister, $fmBrother, $fmGrandparents,
                $fmSpouse, $fmChildren, $currentConcern, $healthIssues, $timelineHistory, $specificQuestions, $medications
            );
        } else {
            $this->create(
                $appointmentId, $patientName, $formDate, $age, $sex, $address, $surroundingArea, $occupation,
                $dietaryPreference, $covidVaccination, $weight, $height, $bmi, $hipCircumference, $waistCircumference,
                $foodSensitivities, $foodAllergies, $craveBinge, $dietFollowed, $otherSensitivities, $toxicities,
                $sleepRegular, $sleepDetails, $fmMother, $fmFather, $fmSister, $fmBrother, $fmGrandparents,
                $fmSpouse, $fmChildren, $currentConcern, $healthIssues, $timelineHistory, $specificQuestions, $medications
            );
        }
    }

    public function findByAppointmentId(int $appointmentId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM patient_intake_forms WHERE appointment_id = :appointment_id LIMIT 1');
        $stmt->bindValue(':appointment_id', $appointmentId, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $row['health_issues'] = !empty($row['health_issues_json']) ? json_decode((string) $row['health_issues_json'], true) : [];
        $row['timeline_history'] = !empty($row['timeline_history_json']) ? json_decode((string) $row['timeline_history_json'], true) : [];
        $row['specific_questions'] = !empty($row['specific_questions_json']) ? json_decode((string) $row['specific_questions_json'], true) : [];
        $row['medications'] = !empty($row['medications_json']) ? json_decode((string) $row['medications_json'], true) : [];
        
        return $row;
    }
}
