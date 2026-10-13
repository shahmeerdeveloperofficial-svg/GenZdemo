import React from 'react';
import { motion } from 'framer-motion';
import { KEY_STATS } from '../data/schoolData';

export const StatsBar: React.FC = () => {
  return (
    <section className="relative z-20 -mt-10 mb-12" aria-label="Key Educational Metrics">
      <div className="max-w-7xl mx-auto px-6">
        <motion.div
          className="bg-navy rounded-3xl p-8 md:p-10 shadow-deep border border-white/10 text-white relative overflow-hidden"
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
        >
          {/* Subtle Radial Glow */}
          <div className="absolute top-0 right-0 w-96 h-full bg-radial-gradient from-cyan/20 to-transparent pointer-events-none" />

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
            {KEY_STATS.map((stat, idx) => (
              <div
                key={stat.id}
                className={`flex flex-col justify-between ${
                  idx < KEY_STATS.length - 1 ? 'lg:border-r lg:border-white/10 lg:pr-6' : ''
                }`}
              >
                <div>
                  <div className="flex items-baseline gap-1 mb-2">
                    {stat.prefix && (
                      <span className="font-display text-2xl font-bold text-cyan-light">
                        {stat.prefix}
                      </span>
                    )}
                    <span className="font-display text-4xl sm:text-5xl font-black text-white tracking-tight">
                      {stat.isRatio ? `1:${stat.number}` : stat.number}
                    </span>
                    {stat.suffix && (
                      <span className="font-display text-2xl font-bold text-cyan-light">
                        {stat.suffix}
                      </span>
                    )}
                  </div>
                  <h4 className="font-display font-bold text-sm text-cyan-light uppercase tracking-wider mb-2">
                    {stat.label}
                  </h4>
                </div>
                <p className="text-white/70 text-xs leading-relaxed max-w-[240px]">
                  {stat.description}
                </p>
              </div>
            ))}
          </div>
        </motion.div>
      </div>
    </section>
  );
};
