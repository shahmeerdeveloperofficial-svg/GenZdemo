import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  ChevronLeft,
  ChevronRight,
  Sparkles,
  ArrowRight,
  Globe2,
  GraduationCap,
  ShieldCheck,
  Plane,
  Building2,
  Award
} from 'lucide-react';
import { getContent, siteAsset } from '../content';

interface HeaderBannersProps {
  onOpenModal?: () => void;
}

export const HeaderBannersSection: React.FC<HeaderBannersProps> = ({ onOpenModal }) => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [isAutoPlaying, setIsAutoPlaying] = useState(true);

  const banners = [
    {
      id: 'banner-1',
      tag: 'Pakistan’s Largest Global Education Network',
      headline: 'Unlock Direct Pathways to Top Ranked World Universities',
      subtitle: 'Full-ride scholarships, Cambridge continuum credentials, and fast-track visa processing for the UK, USA, Australia, and Europe.',
      image: '/assets/images/paragon-banner-01.jpg',
      badge: '100% Visa Assistance',
      accent: 'from-blue-600 to-cyan',
    },
    {
      id: 'banner-2',
      tag: 'Global Study Abroad Horizons',
      headline: 'Architecting Prestigious Scholarships & International Careers',
      subtitle: 'Personalized admissions counseling, Ivy League essay workshops, and Russell Group application guidance from verified specialists.',
      image: '/assets/images/paragon-banner-04.jpg',
      badge: '$12M+ Scholarships Won',
      accent: 'from-cyan to-blue-500',
    },
    {
      id: 'banner-3',
      tag: 'Your Global Education Partner',
      headline: 'Over 850+ Partner Campuses Across 25+ Global Destinations',
      subtitle: 'Transparent visa consultancy, IELTS/PTE preparation, and pre-departure support ensuring seamless transition to student life overseas.',
      image: '/assets/images/paragon-hero-global.webp',
      badge: 'Top Rated In Pakistan',
      accent: 'from-navy via-blue-600 to-cyan',
    },
  ];

  useEffect(() => {
    if (!isAutoPlaying) return;
    const interval = setInterval(() => {
      setCurrentSlide((prev) => (prev + 1) % banners.length);
    }, 6000);
    return () => clearInterval(interval);
  }, [isAutoPlaying, banners.length]);

  const nextSlide = () => {
    setIsAutoPlaying(false);
    setCurrentSlide((prev) => (prev + 1) % banners.length);
  };

  const prevSlide = () => {
    setIsAutoPlaying(false);
    setCurrentSlide((prev) => (prev - 1 + banners.length) % banners.length);
  };

  const slide = banners[currentSlide];

  return (
    <section className="py-16 sm:py-24 bg-gradient-to-b from-white via-slate-50 to-[#FAF7F2] relative overflow-hidden" id="global-banners">
      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Top Header Tag */}
        <div className="flex flex-wrap items-center justify-between gap-4 mb-8">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider">
            <Globe2 className="w-3.5 h-3.5" />
            <span>( Global Study Destinations & Partner Campuses )</span>
          </div>

          {/* Navigation Arrows */}
          <div className="flex items-center gap-2">
            <button
              onClick={prevSlide}
              aria-label="Previous banner"
              className="w-10 h-10 rounded-full bg-white border border-slate-200 text-navy hover:text-cyan hover:border-cyan shadow-sm flex items-center justify-center transition-all hover:scale-105 active:scale-95"
            >
              <ChevronLeft className="w-5 h-5" />
            </button>
            <button
              onClick={nextSlide}
              aria-label="Next banner"
              className="w-10 h-10 rounded-full bg-white border border-slate-200 text-navy hover:text-cyan hover:border-cyan shadow-sm flex items-center justify-center transition-all hover:scale-105 active:scale-95"
            >
              <ChevronRight className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* The Main High-Impact Showcase Card */}
        <div className="relative rounded-[2.5rem] overflow-hidden bg-navy-dark text-white min-h-[440px] sm:min-h-[500px] shadow-2xl border border-white/10 group">
          <AnimatePresence mode="wait">
            <motion.div
              key={slide.id}
              initial={{ opacity: 0, scale: 1.02 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.98 }}
              transition={{ duration: 0.65, ease: [0.16, 1, 0.3, 1] }}
              className="absolute inset-0 w-full h-full"
            >
              {/* High-Resolution Banner Background Image */}
              <img
                src={siteAsset(slide.image)}
                alt={slide.headline}
                className="w-full h-full object-cover object-center filter brightness-90 group-hover:scale-105 transition-transform duration-1000 ease-out"
                loading="lazy"
              />

              {/* Rich Multi-Layer Gradient Overlays for Maximum Typography Legibility */}
              <div className="absolute inset-0 bg-gradient-to-r from-[#030B17]/95 via-[#07172C]/80 to-transparent" />
              <div className="absolute inset-0 bg-gradient-to-t from-[#030B17] via-transparent to-transparent opacity-80" />

              {/* Banner Foreground Content */}
              <div className="absolute inset-0 p-8 sm:p-14 lg:p-16 flex flex-col justify-between max-w-3xl z-10">
                {/* Top Badge */}
                <div>
                  <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan/20 border border-cyan/40 text-cyan-light text-xs font-display font-extrabold uppercase tracking-wider backdrop-blur-md mb-5 shadow-lg">
                    <Sparkles className="w-3.5 h-3.5 text-cyan" />
                    <span>{slide.tag}</span>
                  </div>

                  {/* Headline */}
                  <h3 className="font-display text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-[1.12] mb-4 drop-shadow-md">
                    {slide.headline}
                  </h3>

                  {/* Subtitle */}
                  <p className="text-slate-200 text-xs sm:text-base leading-relaxed max-w-2xl drop-shadow">
                    {slide.subtitle}
                  </p>
                </div>

                {/* Bottom Actions & Floating Highlight */}
                <div className="flex flex-wrap items-center gap-4 pt-6">
                  <button
                    onClick={onOpenModal}
                    className="px-7 py-3.5 bg-cyan hover:bg-cyan-light text-navy rounded-full font-display font-black text-xs uppercase tracking-wider shadow-lg hover:shadow-cyan-glow hover:-translate-y-0.5 transition-all flex items-center gap-2 group/btn"
                  >
                    <span>Apply for 2026–27 Intake</span>
                    <ArrowRight className="w-4 h-4 text-navy transform group-hover/btn:translate-x-1.5 transition-transform" />
                  </button>

                  <div className="px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-bold text-white flex items-center gap-2">
                    <ShieldCheck className="w-4 h-4 text-cyan" />
                    <span>{slide.badge}</span>
                  </div>
                </div>
              </div>
            </motion.div>
          </AnimatePresence>

          {/* Bottom Slide Indicators */}
          <div className="absolute bottom-6 right-6 sm:right-12 z-20 flex items-center gap-2">
            {banners.map((b, i) => (
              <button
                key={b.id}
                onClick={() => {
                  setIsAutoPlaying(false);
                  setCurrentSlide(i);
                }}
                aria-label={`Go to slide ${i + 1}`}
                className={`h-2 rounded-full transition-all duration-300 ${
                  currentSlide === i ? 'w-8 bg-cyan shadow-cyan-glow' : 'w-2 bg-white/40 hover:bg-white/70'
                }`}
              />
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};
