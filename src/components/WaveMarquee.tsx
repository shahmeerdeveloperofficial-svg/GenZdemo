import React from 'react';

export const WaveMarquee: React.FC = () => {
  const values = [
    'Curiosity',
    'Inquiry',
    'Global Citizenship',
    'Innovation',
    'Academic Rigour',
    'Integrity',
    'Leadership',
    'Excellence',
    'Exploration',
    'Resilience'
  ];

  return (
    <section className="bg-navy py-6 overflow-hidden relative border-y border-white/10" aria-label="Core Values Marquee">
      <div className="flex overflow-hidden select-none gap-8">
        <div className="animate-marquee flex items-center gap-8 whitespace-nowrap">
          {values.map((val) => (
            <span
              key={val}
              className="inline-flex items-center gap-8 font-display text-lg sm:text-2xl font-bold uppercase tracking-wider text-white/90"
            >
              <span>{val}</span>
              <span className="w-2 h-2 rounded-full bg-cyan-light" />
            </span>
          ))}
        </div>
        <div className="animate-marquee flex items-center gap-8 whitespace-nowrap" aria-hidden="true">
          {values.map((val) => (
            <span
              key={`repeat-${val}`}
              className="inline-flex items-center gap-8 font-display text-lg sm:text-2xl font-bold uppercase tracking-wider text-white/90"
            >
              <span>{val}</span>
              <span className="w-2 h-2 rounded-full bg-cyan-light" />
            </span>
          ))}
        </div>
      </div>
    </section>
  );
};
