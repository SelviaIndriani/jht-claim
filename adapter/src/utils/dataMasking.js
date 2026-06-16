/**
 * Data Masking Utility untuk hide sensitive information di logs
 */

const SENSITIVE_FIELDS = [
  'password', 'token', 'api_key', 'secret', 'private_key',
  'email', 'phone', 'mobile', 'nik', 'no_bpjs', 'no_rekening',
  'credit_card', 'cvv', 'pin', 'otp',
  'nama_ibu_kandung', 'tempat_lahir',
  'authorization', 'x-api-key', 'cookie',
];

/**
 * Mask sensitive data dalam object
 */
const mask = (data) => {
  if (!data) return data;

  if (typeof data !== 'object') return data;

  if (Array.isArray(data)) {
    return data.map(item => mask(item));
  }

  const masked = {};

  for (const key in data) {
    if (data.hasOwnProperty(key)) {
      if (isSensitiveField(key)) {
        masked[key] = maskValue(data[key], key);
      } else if (typeof data[key] === 'object' && data[key] !== null) {
        masked[key] = mask(data[key]);
      } else {
        masked[key] = data[key];
      }
    }
  }

  return masked;
};

/**
 * Check if field name is sensitive
 */
const isSensitiveField = (fieldName) => {
  const fieldLower = fieldName.toLowerCase();
  return SENSITIVE_FIELDS.some(sensitive => fieldLower.includes(sensitive));
};

/**
 * Mask single value
 */
const maskValue = (value, fieldName = '') => {
  if (!value) return value;

  const strValue = String(value);

  // Email
  if (isEmail(strValue)) {
    return maskEmail(strValue);
  }

  // NIK
  if (fieldName === 'nik' || isNik(strValue)) {
    return maskNik(strValue);
  }

  // BPJS
  if (fieldName === 'no_bpjs' || isBpjs(strValue)) {
    return maskBpjs(strValue);
  }

  // Phone
  if (isPhone(strValue)) {
    return maskPhone(strValue);
  }

  // Credit Card
  if (isCreditCard(strValue)) {
    return maskCreditCard(strValue);
  }

  // Generic
  return maskGeneric(strValue);
};

/**
 * Masking strategies
 */
const maskEmail = (email) => {
  if (!email.includes('@')) return '***@***';

  const [user, domain] = email.split('@');
  const maskedUser = user.length <= 2
    ? '*'.repeat(user.length)
    : user.substring(0, 2) + '*'.repeat(user.length - 2);

  return `${maskedUser}@${domain}`;
};

const maskNik = (nik) => {
  if (nik.length < 8) return '*'.repeat(nik.length);
  return nik.substring(0, 8) + '*'.repeat(nik.length - 12) + nik.substring(nik.length - 4);
};

const maskBpjs = (bpjs) => {
  if (bpjs.length < 8) return '*'.repeat(bpjs.length);
  return bpjs.substring(0, 4) + '*'.repeat(bpjs.length - 7) + bpjs.substring(bpjs.length - 3);
};

const maskPhone = (phone) => {
  if (phone.length < 6) return '*'.repeat(phone.length);
  return phone.substring(0, 5) + '*'.repeat(phone.length - 9) + phone.substring(phone.length - 4);
};

const maskCreditCard = (card) => {
  const cleaned = card.replace(/\D/g, '');
  if (cleaned.length < 8) return '*'.repeat(cleaned.length);
  return cleaned.substring(0, 4) + '*'.repeat(cleaned.length - 8) + cleaned.substring(cleaned.length - 4);
};

const maskGeneric = (value) => {
  if (value.length <= 2) return '*'.repeat(value.length);
  return '*'.repeat(value.length - 2) + value.substring(value.length - 2);
};

/**
 * Validation checks
 */
const isEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

const isNik = (value) => /^\d{16}$/.test(value);

const isBpjs = (value) => /^\d{11}$/.test(value);

const isPhone = (value) => /^(\+62|0)[0-9]{9,12}$/.test(value);

const isCreditCard = (value) => {
  const cleaned = value.replace(/\D/g, '');
  return cleaned.length >= 13 && cleaned.length <= 19;
};

module.exports = { mask, maskValue, isSensitiveField };
