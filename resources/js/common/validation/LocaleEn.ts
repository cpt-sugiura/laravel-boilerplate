export const LocaleEn = {
  mixed: {
    default: '${path} is invalid',
    required: '${path} is required',
    oneOf: '${path} must be one of the following values:${values}',
    notOneOf: '${path} must not be one of the following values:${values}',
  },
  string: {
    length: '${path} must be exactly ${length} characters',
    min: '${path} must be at least ${min} characters',
    max: '${path} must be at most ${max} characters',
    matches: '${path} must match: "${regex}"',
    email: '${path} must be in the form of an email address',
    url: '${path} must be a valid URL',
    trim: '${path} must be a trimmed string',
    lowercase: '${path} must be a lowercase string',
    uppercase: '${path} must be an uppercase string',
  },
  number: {
    min: '${path} must be greater than or equal to ${min}',
    max: '${path} must be less than or equal to ${max}',
    lessThan: '${path} must be less than ${less}',
    moreThan: '${path} must be greater than ${more}',
    notEqual: '${path} must not be equal to ${notEqual}',
    positive: '${path} must be a positive number',
    negative: '${path} must be a negative number',
    integer: '${path} must be an integer',
  },
  date: {
    min: '${path} field must be after ${min}',
    max: '${path} field must be before ${max}',
  },
  object: {
    noUnknown: 'The ${path} field cannot contain a key that is not specified in the object shape',
  },
  array: {
    min: '${path} field must have at least ${min} entries',
  },
};
