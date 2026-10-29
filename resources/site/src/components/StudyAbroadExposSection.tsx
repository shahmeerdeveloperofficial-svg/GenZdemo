import React, { useState, useEffect, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
  ChevronLeft,
  ChevronRight,
  Calendar,
  Clock,
  MapPin,
  Sparkles,
  ArrowRight,
  MessageCircle,
  Pause,
  Play
} from 'lucide-react';
import { getContent, siteAsset, route } from '../content';

interface ExpoItem {
  id: string;
  city: string;
  title: string;
  time: string;
  date: string;
  location: string;
  image: string;
  badge?: string;
}

interface StudyAbroadExposProps {
  onOpenModal?: (eventTitle?: string) => void;
}

export const StudyAbroadExposSection: React.FC<StudyAbroadExposProps> = ({ onOpenModal }) => {
  const defaultExpos: ExpoItem[] = [
    {
      id: 'expo-faisalabad',
      city: 'FAISALABAD',
      title: 'Faisalabad Study Abroad Expo 2026',
      time: '12:00 PM TO 07:00 PM',
      date: 'Tuesday, 17 Nov 2026',
      location: 'Serena Hotel – Faisalabad',
      image: '/assets/images/expo-faisalabad.png',
      badge: 'Popular',
    },
    {
      id: 'expo-gujranwala',
      city: 'GUJRANWALA',
      title: 'Gujranwala Study Abroad Expo 2026',
      time: '12:00 PM TO 07:00 PM',
      date: 'Saturday, 21 Nov 2026',
      location: 'GenZ Executive Office Near Gift University – Gujranwala',
      image: '/assets/images/expo-gujranwala.png',
      badge: 'High Demand',
    },
    {
      id: 'expo-gujrat',
      city: 'GUJRAT',
      title: 'Gujrat Study Abroad Expo 2026',
      time: '12:00 PM TO 07:00 PM',
      date: 'Wednesday, 18 Nov 2026',
      location: 'GenZ Regional Center – Gujrat',
      image: '/assets/images/expo-gujrat.png',
      badge: 'Fast Filling',
    },
    {
      id: 'expo-lahore',
      city: 'LAHORE',
      title: 'Lahore Grand Study Abroad Expo 2026',
      time: '11:00 AM TO 08:00 PM',
      date: 'Sunday, 22 Nov 2026',
      location: 'Pearl Continental (PC Hotel) – Lahore',
      image: '/assets/images/expo-faisalabad.png',
      badge: 'Flagship Event',
    },
    {
      id: 'expo-islamabad',
      city: 'ISLAMABAD',
      title: 'Islamabad Global University Fair 2026',
      time: '11:00 AM TO 07:00 PM',
      date: 'Thursday, 26 Nov 2026',
      location: 'Serena Hotel – Islamabad',
      image: '/assets/images/expo-gujranwala.png',
      badge: 'Capital Edition',
    },
    {
      id: 'expo-multan',
      city: 'MULTAN',
      title: 'Multan Study Abroad Gala 2026',
      time: '12:00 PM TO 07:00 PM',
      date: 'Monday, 30 Nov 2026',
      location: 'Ramada by Wyndham – Multan',
      image: '/assets/images/expo-gujrat.png',
      badge: 'Limited Seats',
    },
  ];

  const expos = getContent('sections.expos.items', defaultExpos);
  const [startIndex, setStartIndex] = useState(0);
  const [direction, setDirection] = useState<'left' | 'right'>('left');
  const [isPaused, setIsPaused] = useState(false);
  const [itemsPerPage, setItemsPerPage] = useState(3);

  // Responsive items count
  useEffect(() => {
    const handleResize = () => {
      if (window.innerWidth < 768) {
        setItemsPerPage(1);
      } else if (window.innerWidth < 1024) {
        setItemsPerPage(2);
      } else {
        setItemsPerPage(3);
      }
    };
    handleResize();
    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  // Auto-scroll animation: scrolls to left every 3.5 seconds
  useEffect(() => {
    if (isPaused) return;
    const interval = setInterval(() => {
      setDirection('left');
      setStartIndex((prev) => (prev + 1) % expos.length);
    }, 3500);

    return () => clearInterval(interval);
  }, [isPaused, expos.length]);

  const handlePrev = () => {
    setDirection('right');
    setStartIndex((prev) => (prev > 0 ? prev - 1 : expos.length - 1));
  };

  const handleNext = () => {
    setDirection('left');
    setStartIndex((prev) => (prev + 1) % expos.length);
  };

  const handleSelectExpo = (title: string) => {
    document.dispatchEvent(new CustomEvent('genz:inquiry', { detail: { event: title } }));
    if (onOpenModal) {
      onOpenModal(title);
    }
  };

  // Build visible items with circular wrapping
  const visibleExpos = [];
  for (let i = 0; i < itemsPerPage; i++) {
    const idx = (startIndex + i) % expos.length;
    visibleExpos.push({ ...expos[idx], slotIndex: i });
  }

  return (
    <section
      className="py-24 sm:py-32 bg-[#3F2666] relative overflow-hidden text-white scroll-mt-24"
      id="events"
      onMouseEnter={() => setIsPaused(true)}
      onMouseLeave={() => setIsPaused(false)}
    >
      {/* Background Ambient Violet & Amber Glows */}
      <div className="absolute top-0 left-1/4 w-[32rem] h-[32rem] bg-purple-500/15 rounded-full blur-[150px] pointer-events-none" />
      <div className="absolute bottom-0 right-1/4 w-[34rem] h-[34rem] bg-amber-500/12 rounded-full blur-[160px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Top Header Pill & Heading */}
        <div className="text-center mb-12 sm:mb-16">
          <div className="inline-block mb-5">
            <button
              onClick={() => window.location.assign(route('events'))}
              className="px-8 py-2.5 rounded-full bg-[#FFB703] hover:bg-[#FFA500] text-slate-950 font-display font-bold text-xs sm:text-sm tracking-wide shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all duration-300"
            >
              View all events
            </button>
          </div>

          <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.18] mb-4">
            GenZ Study Abroad Expos{' '}
            <span className="text-[#FFB703]">2026</span>
          </h2>
          <p className="text-purple-200/90 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
            Meet official university delegates from the UK, USA, Canada, and Australia.
            Avail on-the-spot profile assessments, instant conditional offers, and up to 100% scholarships.
          </p>
        </div>

        {/* Carousel Container with Absolute Navigation Arrows */}
        <div className="relative">
          {/* Left Arrow Button */}
          <button
            onClick={handlePrev}
            aria-label="Previous Events"
            className="absolute -left-3 sm:-left-6 top-1/2 -translate-y-1/2 z-30 w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-[#FFB703] hover:bg-[#FFA500] text-slate-950 flex items-center justify-center shadow-xl shadow-amber-500/30 hover:scale-110 active:scale-95 transition-all duration-300 group"
          >
            <ChevronLeft className="w-6 h-6 stroke-[2.5] group-hover:-translate-x-0.5 transition-transform" />
          </button>

          {/* Right Arrow Button */}
          <button
            onClick={handleNext}
            aria-label="Next Events"
            className="absolute -right-3 sm:-right-6 top-1/2 -translate-y-1/2 z-30 w-11 h-11 sm:w-13 sm:h-13 rounded-full bg-[#FFB703] hover:bg-[#FFA500] text-slate-950 flex items-center justify-center shadow-xl shadow-amber-500/30 hover:scale-110 active:scale-95 transition-all duration-300 group"
          >
            <ChevronRight className="w-6 h-6 stroke-[2.5] group-hover:translate-x-0.5 transition-transform" />
          </button>

          {/* Cards Grid with Leftward Sliding Animation */}
          <div className="overflow-hidden py-2 px-1">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10">
              <AnimatePresence mode="popLayout" custom={direction}>
                {visibleExpos.map((expo) => (
                  <motion.div
                    key={`${expo.id}-${expo.slotIndex}`}
                    custom={direction}
                    initial={{
                      opacity: 0,
                      x: direction === 'left' ? 70 : -70,
                      scale: 0.95
                    }}
                    animate={{
                      opacity: 1,
                      x: 0,
                      scale: 1,
                      transition: {
                        duration: 0.65,
                        ease: [0.25, 1, 0.5, 1]
                      }
                    }}
                    exit={{
                      opacity: 0,
                      x: direction === 'left' ? -70 : 70,
                      scale: 0.95,
                      transition: {
                        duration: 0.5,
                        ease: [0.25, 1, 0.5, 1]
                      }
                    }}
                    className="flex flex-col justify-between group bg-purple-950/20 p-5 sm:p-6 rounded-3xl border border-white/5 hover:border-amber-400/30 hover:bg-purple-950/40 transition-all duration-300 shadow-xl"
                  >
                    {/* Top Graphic Area: Circular Landmark Silhouette Badge */}
                    <div className="relative flex items-center justify-center mb-6">
                      <div className="w-full max-w-[280px] h-[190px] flex items-center justify-center overflow-hidden transition-transform duration-500 group-hover:scale-105">
                        <img
                          src={siteAsset(expo.image)}
                          alt={expo.title}
                          className="w-full h-full object-contain filter drop-shadow-[0_10px_20px_rgba(0,0,0,0.3)]"
                          loading="lazy"
                        />
                      </div>
                    </div>

                    {/* Card Content & Details */}
                    <div className="flex-1 flex flex-col justify-between">
                      <div>
                        {/* Expo Title */}
                        <h3 className="font-display text-xl sm:text-2xl font-bold text-white mb-3 group-hover:text-[#FFB703] transition-colors leading-snug">
                          {expo.title}
                        </h3>

                        {/* Timing & Date */}
                        <div className="text-purple-200 text-xs sm:text-sm font-medium mb-3 flex flex-wrap items-center gap-x-3 gap-y-1">
                          <span className="flex items-center gap-1.5 text-purple-100">
                            <Clock className="w-3.5 h-3.5 text-[#FFB703]" />
                            <span>{expo.time}</span>
                          </span>
                          <span className="text-purple-300">•</span>
                          <span className="flex items-center gap-1.5 text-purple-100">
                            <Calendar className="w-3.5 h-3.5 text-[#FFB703]" />
                            <span>Date: {expo.date}</span>
                          </span>
                        </div>

                        {/* Location */}
                        <div className="text-purple-200/90 text-xs sm:text-sm mb-6 flex items-start gap-1.5">
                          <MapPin className="w-4 h-4 text-[#FFB703] flex-shrink-0 mt-0.5" />
                          <span>Location: {expo.location}</span>
                        </div>
                      </div>

                      {/* Bottom Action Button: More Details */}
                      <div>
                        <button
                          onClick={() => handleSelectExpo(expo.title)}
                          className="px-7 py-3 rounded-lg sm:rounded-xl bg-[#FFB703] hover:bg-[#FFA500] text-slate-950 font-display font-bold text-xs sm:text-sm shadow-md hover:shadow-amber-500/25 active:scale-95 transition-all duration-300 flex items-center justify-center gap-2 group/btn w-fit"
                        >
                          <span>More Details</span>
                          <ArrowRight className="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
                        </button>
                      </div>
                    </div>
                  </motion.div>
                ))}
              </AnimatePresence>
            </div>
          </div>

          {/* Carousel Pagination Indicator Dots */}
          <div className="flex items-center justify-center gap-2 mt-8">
            {expos.map((expo, idx) => {
              const isActive = idx === startIndex;
              return (
                <button
                  key={expo.id}
                  onClick={() => {
                    setDirection(idx > startIndex ? 'left' : 'right');
                    setStartIndex(idx);
                  }}
                  aria-label={`Go to ${expo.city}`}
                  className={`h-2.5 rounded-full transition-all duration-300 ${
                    isActive
                      ? 'w-8 bg-[#FFB703]'
                      : 'w-2.5 bg-white/25 hover:bg-white/50'
                  }`}
                />
              );
            })}
          </div>
        </div>

        {/* Bottom Strip: "Get Free Consultation" (Purple Pill) + "Chat with us" (Red Pill) */}
        <div className="mt-14 pt-10 border-t border-purple-400/20 flex flex-col sm:flex-row items-center justify-between gap-6">
          {/* Left: Free entry announcement */}
          <div className="flex items-center gap-3 text-purple-200 text-xs sm:text-sm">
            <div className="w-3 h-3 rounded-full bg-emerald-400 animate-ping" />
            <span className="font-semibold text-white">
              Free Entry For Students & Parents
            </span>
            <span className="hidden md:inline text-purple-300">
              — Bring your academic transcripts for on-the-spot evaluation
            </span>
          </div>

          {/* Right: Signature Action Buttons */}
          <div className="flex flex-wrap items-center gap-3">
            {/* Screenshot 1 Reference: Deep Purple Pill "Get Free Consultation" */}
            <button
              onClick={() => {
                document.dispatchEvent(new CustomEvent('genz:inquiry', { detail: { event: 'Free Consultation' } }));
                onOpenModal?.('Free Consultation');
              }}
              className="px-6 py-2.5 rounded-full bg-[#2B1847] hover:bg-[#201138] border border-purple-400/40 text-white font-display font-bold text-xs sm:text-sm shadow-lg hover:shadow-purple-900/50 hover:scale-105 active:scale-95 transition-all duration-300 flex items-center gap-2"
            >
              <Sparkles className="w-3.5 h-3.5 text-[#FFB703]" />
              <span>Get Free Consultation</span>
            </button>

            {/* Chat with us (Red Pill Button from Times Consultant Screenshot) */}
            <a
              href="https://wa.me/923000000000"
              target="_blank"
              rel="noopener noreferrer"
              className="px-5 py-2.5 rounded-full bg-[#E50914] hover:bg-[#CC0812] text-white font-display font-bold text-xs sm:text-sm shadow-lg hover:scale-105 active:scale-95 transition-all duration-300 flex items-center gap-2"
            >
              <MessageCircle className="w-4 h-4 fill-white text-white" />
              <span>Chat with us</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  );
};
