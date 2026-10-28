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
  Award,
  Plane,
  Star
} from 'lucide-react';
import { siteAsset } from '../content';

interface HeaderBannersProps {
  onOpenModal?: () => void;
}

export const HeaderBannersSection: React.FC<HeaderBannersProps> = ({ onOpenModal }) => {
  const [currentSlide, setCurrentSlide] = useState(0);
  const [isAutoPlaying, setIsAutoPlaying] = useState(true);

  const banners = [
    {
      id: 'banner-1',
      tag: "Pakistan’s Largest Global Education Network",
      tagColor: 'bg-emerald-500/25 border-emerald-400/50 text-emerald-300',
      headline: 'Unlock Direct Pathways to Top Ranked World Universities',
      subtitle: 'Full-ride scholarships, Cambridge continuum credentials, and fast-track visa processing for the UK, USA, Australia, and Europe.',
      image: '/assets/images/campus-building.jpg',
      badge: '100% Visa Assistance Guaranteed',
      badgeClass: 'bg-emerald-500/20 border-emerald-400/40 text-emerald-300',
      overlayGrad: 'from-[#030E1F]/92 via-[#081B38]/70 to-transparent',
      accentColor: 'emerald',
    },
    {
      id: 'banner-2',
      tag: 'Global Study Abroad Horizons',
      tagColor: 'bg-amber-500/25 border-amber-400/50 text-amber-300',
      headline: 'Architecting Prestigious Scholarships & International Careers',
      subtitle: 'Personalized admissions counseling, Ivy League essay workshops, and Russell Group application guidance from verified specialists.',
      image: '/assets/images/hero-campus.jpg',
      badge: '$12M+ Scholarships Won',
      badgeClass: 'bg-amber-500/20 border-amber-400/40 text-amber-300',
      overlayGrad: 'from-[#0F0826]/92 via-[#1B0F3E]/70 to-transparent',
      accentColor: 'amber',
    },
    {
      id: 'banner-3',
      tag: 'Your Global Education Partner',
      tagColor: 'bg-cyan-500/25 border-cyan-400/50 text-cyan-300',
      headline: 'Over 850+ Partner Campuses Across 25+ Global Destinations',
      subtitle: 'Transparent visa consultancy, IELTS/PTE preparation, and pre-departure support ensuring seamless transition to student life overseas.',
      image: '/assets/images/destination-img-1.png',
      badge: 'Top Rated In Pakistan',
      badgeClass: 'bg-cyan-500/20 border-cyan-400/40 text-cyan-300',
      overlayGrad: 'from-[#04161C]/92 via-[#092B35]/70 to-transparent',
      accentColor: 'cyan',
    },
  ];

  const destinationPills = [
    { name: 'United Kingdom', flag: '🇬🇧', code: 'UK' },
    { name: 'United States', flag: '🇺🇸', code: 'USA' },
    { name: 'Canada', flag: '🇨🇦', code: 'CAN' },
    { name: 'Australia', flag: '🇦🇺', code: 'AUS' },
    { name: 'Germany', flag: '🇩🇪', code: 'DE' },
    { name: 'Ireland', flag: '🇮🇪', code: 'IRL' },
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
    <section className="py-16 sm:py-24 bg-gradient-to-b from-[#F5F8FA] via-white to-[#FAF7F2] relative overflow-hidden" id="global-banners">
      {/* Background ambient multi-color light glows */}
      <div className="absolute top-10 left-10 w-96 h-96 bg-amber-300/10 rounded-full blur-[140px] pointer-events-none" />
      <div className="absolute bottom-10 right-10 w-96 h-96 bg-emerald-300/10 rounded-full blur-[140px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Top Header Row with Destination Flag Chips */}
        <div className="flex flex-wrap items-center justify-between gap-4 mb-6">
          <div className="flex flex-wrap items-center gap-2 sm:gap-3">
            <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gradient-to-r from-amber-500/15 to-orange-500/15 border border-amber-400/40 text-amber-800 text-xs font-display font-bold uppercase tracking-wider shadow-sm">
              <Globe2 className="w-3.5 h-3.5 text-amber-600" />
              <span>( Global Study Destinations & Partner Campuses )</span>
            </div>

            {/* Quick Country Pills */}
            <div className="hidden md:flex items-center gap-1.5">
              {destinationPills.map((d) => (
                <span
                  key={d.code}
                  className="px-2.5 py-1 rounded-full bg-white border border-slate-200 text-slate-700 text-[11px] font-bold shadow-2xs hover:border-amber-400 transition-colors cursor-default"
                >
                  <span className="mr-1">{d.flag}</span>
                  {d.code}
                </span>
              ))}
            </div>
          </div>

          {/* Navigation Arrows */}
          <div className="flex items-center gap-2">
            <button
              onClick={prevSlide}
              aria-label="Previous banner"
              className="w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-800 hover:text-amber-600 hover:border-amber-400 shadow-sm flex items-center justify-center transition-all hover:scale-105 active:scale-95"
            >
              <ChevronLeft className="w-5 h-5" />
            </button>
            <button
              onClick={nextSlide}
              aria-label="Next banner"
              className="w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-800 hover:text-amber-600 hover:border-amber-400 shadow-sm flex items-center justify-center transition-all hover:scale-105 active:scale-95"
            >
              <ChevronRight className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* The Main High-Impact Banner Card with High Color Contrast */}
        <div className="relative rounded-[2.5rem] overflow-hidden bg-slate-950 text-white min-h-[460px] sm:min-h-[500px] shadow-[0_20px_50px_rgba(0,0,0,0.18)] border-2 border-slate-200/80 group">
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
                className="w-full h-full object-cover object-center filter brightness-100 contrast-105 group-hover:scale-105 transition-transform duration-1000 ease-out"
                loading="lazy"
              />

              {/* Crisp Gradient Overlays: Dark Left for Text Readability, Clear Right for Image Vibrancy */}
              <div className={`absolute inset-0 bg-gradient-to-r ${slide.overlayGrad} pointer-events-none`} />
              <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 pointer-events-none" />

              {/* Banner Foreground Content */}
              <div className="absolute inset-0 p-8 sm:p-14 lg:p-16 flex flex-col justify-between max-w-3xl z-10">
                {/* Top Badge */}
                <div>
                  <div className={`inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-xs font-display font-extrabold uppercase tracking-wider backdrop-blur-md mb-5 shadow-lg ${slide.tagColor}`}>
                    <Sparkles className="w-3.5 h-3.5" />
                    <span>{slide.tag}</span>
                  </div>

                  {/* Headline */}
                  <h3 className="font-display text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-[1.12] mb-4 drop-shadow-md">
                    {slide.headline}
                  </h3>

                  {/* Subtitle */}
                  <p className="text-slate-100 text-xs sm:text-base leading-relaxed max-w-2xl drop-shadow">
                    {slide.subtitle}
                  </p>
                </div>

                {/* Bottom Actions with Warm Sunset Button */}
                <div className="flex flex-wrap items-center gap-4 pt-6">
                  <button
                    onClick={onOpenModal}
                    className="px-8 py-4 bg-gradient-to-r from-amber-400 via-orange-500 to-amber-500 hover:from-amber-300 hover:to-orange-400 text-slate-950 rounded-full font-display font-black text-xs uppercase tracking-wider shadow-lg shadow-orange-500/35 hover:shadow-orange-500/50 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 group/btn cursor-pointer"
                  >
                    <span>Apply for 2026–27 Intake</span>
                    <ArrowRight className="w-4 h-4 text-slate-950 transform group-hover/btn:translate-x-1.5 transition-transform" />
                  </button>

                  <div className={`px-4 py-2.5 rounded-full backdrop-blur-md border text-xs font-bold flex items-center gap-2 ${slide.badgeClass}`}>
                    <ShieldCheck className="w-4 h-4" />
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
                className={`h-2.5 rounded-full transition-all duration-300 ${
                  currentSlide === i ? 'w-9 bg-gradient-to-r from-amber-400 to-orange-500 shadow-lg' : 'w-2.5 bg-white/40 hover:bg-white/80'
                }`}
              />
            ))}
          </div>
        </div>
      </div>
    </section>
  );
};
