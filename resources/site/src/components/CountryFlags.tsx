import React from 'react';

export const USFlag: React.FC<{ className?: string }> = ({ className = 'w-full h-full' }) => (
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" className={className}>
    <path fill="#bd3d44" d="M0 0h640v480H0" />
    <path stroke="#fff" strokeWidth="37" d="M0 55.3h640M0 129h640M0 203h640M0 277h640M0 351h640M0 425h640" />
    <path fill="#192f5d" d="M0 0h364.8v258.5H0" />
    <marker id="us-flag-star" markerHeight="30" markerWidth="30">
      <path fill="#fff" d="m14 0 9 27L0 10h28L5 27z" />
    </marker>
    <path
      fill="none"
      markerMid="url(#us-flag-star)"
      d="m0 0 16 11h61 61 61 61 60L47 37h61 61 60 61L16 63h61 61 61 61 60L47 89h61 61 60 61L16 115h61 61 61 61 60L47 141h61 61 60 61L16 166h61 61 61 61 60L47 192h61 61 60 61L16 218h61 61 61 61 60z"
    />
  </svg>
);

export const UKFlag: React.FC<{ className?: string }> = ({ className = 'w-full h-full' }) => (
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" className={className}>
    <path fill="#012169" d="M0 0h640v480H0z" />
    <path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0z" />
    <path fill="#C8102E" d="m424 281 216 159v40L369 281zm-184 20 6 35L54 480H0zM640 0v3L391 191l2-44L590 0zM0 0l239 176h-60L0 42z" />
    <path fill="#FFF" d="M241 0v480h160V0zM0 160v160h640V160z" />
    <path fill="#C8102E" d="M0 193v96h640v-96zM273 0v480h96V0z" />
  </svg>
);

export const MalaysiaFlag: React.FC<{ className?: string }> = ({ className = 'w-full h-full' }) => (
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" className={className}>
    <defs>
      <clipPath id="my-flag-clip">
        <path fill="#fff" d="M0 0h640v480H0z" />
      </clipPath>
    </defs>
    <g clipPath="url(#my-flag-clip)">
      <path fill="#C00" d="M0 0h640v480H0z" />
      <path
        fill="#fff"
        d="M0 34.3h640v34.3H0zm0 68.6h640v34.1H0zm0 68.5h640v34.3H0zm0 68.6h640v34.3H0zm0 68.6h640v34.3H0zm0 68.5h640v34.1H0zm0 68.6h640v34.3H0z"
      />
      <path fill="#006" d="M0 .5h320v274.3H0z" />
      <path
        fill="#FC0"
        d="m207.5 73.8 6 40.7 23-34-12.4 39.2 35.5-20.8-28.1 30 41-3.2-38.3 14.8 38.3 14.8-41-3.2 28.1 30-35.5-20.8 12.3 39.3-23-34.1-6 40.7-5.9-40.7-23 34 12.4-39.2-35.5 20.8 28-30-41 3.2 38.4-14.8-38.3-14.8 41 3.2-28.1-30 35.5 20.8-12.4-39.3 23 34.1zm-33.3 1.7a71 71 0 0 0-100 65 71 71 0 0 0 100 65 80 80 0 0 1-83.2 6.2 80 80 0 0 1-43.4-71.2 80 80 0 0 1 126.6-65"
      />
    </g>
  </svg>
);

export const TurkeyFlag: React.FC<{ className?: string }> = ({ className = 'w-full h-full' }) => (
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" className={className}>
    <g fillRule="evenodd">
      <path fill="#e30a17" d="M0 0h640v480H0z" />
      <path fill="#fff" d="M407 247.5c0 66.2-54.6 119.9-122 119.9s-122-53.7-122-120 54.6-119.8 122-119.8 122 53.7 122 119.9" />
      <path fill="#e30a17" d="M413 247.5c0 53-43.6 95.9-97.5 95.9s-97.6-43-97.6-96 43.7-95.8 97.6-95.8 97.6 42.9 97.6 95.9z" />
      <path fill="#fff" d="m430.7 191.5-1 44.3-41.3 11.2 40.8 14.5-1 40.7 26.5-31.8 40.2 14-23.2-34.1 28.3-33.9-43.5 12-25.8-37z" />
    </g>
  </svg>
);

export const DenmarkFlag: React.FC<{ className?: string }> = ({ className = 'w-full h-full' }) => (
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" className={className}>
    <rect width="640" height="480" fill="#c60c30" />
    <rect width="70" height="480" x="190" fill="#ffffff" />
    <rect width="640" height="70" y="205" fill="#ffffff" />
  </svg>
);

export const PakistanFlag: React.FC<{ className?: string }> = ({ className = 'w-full h-full' }) => (
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" className={className}>
    <rect width="640" height="480" fill="#01411C" />
    <rect width="160" height="480" fill="#ffffff" />
    <g transform="translate(400, 240) rotate(-45)">
      {/* Crescent */}
      <circle cx="0" cy="0" r="110" fill="#ffffff" />
      <circle cx="32" cy="-18" r="96" fill="#01411C" />
      {/* Star */}
      <polygon
        fill="#ffffff"
        points="
          20,-75 29,-45 60,-45 35,-27 44,3 20,-15 -4,3 5,-27 -20,-45 11,-45
        "
        transform="translate(15, -45) scale(0.65)"
      />
    </g>
  </svg>
);

export const getCountryFlag = (country: string) => {
  const c = country.toUpperCase();
  if (c.includes('DENMARK') || c.includes('DK')) return DenmarkFlag;
  if (c.includes('PAKISTAN') || c.includes('PK')) return PakistanFlag;
  if (c.includes('USA') || c.includes('UNITED STATES')) return USFlag;
  if (c.includes('UK') || c.includes('BRITAIN') || c.includes('UNITED KINGDOM')) return UKFlag;
  if (c.includes('MALAYSIA')) return MalaysiaFlag;
  if (c.includes('TURKEY') || c.includes('TURKIYE')) return TurkeyFlag;
  return null;
};

