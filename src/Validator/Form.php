<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Form extends Constraint
{
    public string $notLinkValue = 'Vous n\'avez pas saisi un mail de chez LinkValue. Veuillez recommencer.';
}
