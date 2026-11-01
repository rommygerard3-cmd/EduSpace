<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Teacher;

class ClassAccess
{
    public function classIds(Teacher $teacher): array
    {
        return ClassSubject::where('teacher_id', $teacher->id)
            ->pluck('school_class_id')
            ->unique()
            ->values()
            ->all();
    }

    public function subjectIds(Teacher $teacher, ?int $classId = null): array
    {
        return ClassSubject::where('teacher_id', $teacher->id)
            ->when($classId, fn ($query) => $query->where('school_class_id', $classId))
            ->pluck('subject_id')
            ->unique()
            ->values()
            ->all();
    }

    public function classes(Teacher $teacher)
    {
        return SchoolClass::whereIn('id', $this->classIds($teacher))->orderBy('name');
    }

    public function teachesClass(Teacher $teacher, int $classId): bool
    {
        return ClassSubject::where('teacher_id', $teacher->id)->where('school_class_id', $classId)->exists();
    }

    public function teachesSubject(Teacher $teacher, int $classId, int $subjectId): bool
    {
        return ClassSubject::where('teacher_id', $teacher->id)
            ->where('school_class_id', $classId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    public function canManageAssessment(Teacher $teacher, Assessment $assessment): bool
    {
        return $this->teachesSubject($teacher, $assessment->school_class_id, $assessment->subject_id)
            && ($assessment->teacher_id === null || $assessment->teacher_id === $teacher->id);
    }
}
