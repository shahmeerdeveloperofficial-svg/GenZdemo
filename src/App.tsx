import React, { useState } from 'react';
import { SmoothScroll } from './components/SmoothScroll';
import { Header } from './components/Header';
import { HeroScrollPinned } from './components/HeroScrollPinned';
import { StatsBar } from './components/StatsBar';
import { LMSAndCRMSection } from './components/LMSAndCRMSection';
import { WelcomeSection } from './components/WelcomeSection';
import { PhilosophySection } from './components/PhilosophySection';
import { WhyUsSection } from './components/WhyUsSection';
import { SuccessStoriesSection } from './components/SuccessStoriesSection';
import { CampusLifeSection } from './components/CampusLifeSection';
import { AdmissionsFlightPlan } from './components/AdmissionsFlightPlan';
import { WaveMarquee } from './components/WaveMarquee';
import { NewsEventsSection } from './components/NewsEventsSection';
import { CallToAction } from './components/CallToAction';
import { Footer } from './components/Footer';
import { InquiryModal } from './components/InquiryModal';
import { PortalModal } from './components/PortalModal';
import { FloatingActions } from './components/FloatingActions';

export const App: React.FC = () => {
  const [modalOpen, setModalOpen] = useState(false);
  const [portalModalOpen, setPortalModalOpen] = useState(false);
  const [portalRole, setPortalRole] = useState<'student' | 'parent' | 'crm' | 'faculty'>('student');

  const handleOpenModal = () => setModalOpen(true);
  const handleCloseModal = () => setModalOpen(false);

  const handleOpenPortalModal = (role: 'student' | 'parent' | 'crm' | 'faculty' = 'student') => {
    setPortalRole(role);
    setPortalModalOpen(true);
  };
  const handleClosePortalModal = () => setPortalModalOpen(false);

  return (
    <SmoothScroll>
      <div className="min-h-screen bg-cream font-sans text-navy selection:bg-cyan selection:text-white">
        {/* Navigation Header */}
        <Header onOpenModal={handleOpenModal} />

        <main>
          {/* Signature LA International Style: Pinned 3D Interactive Scroll Globe Continuum */}
          <HeroScrollPinned onOpenModal={handleOpenModal} />

          {/* Key Performance Indicators */}
          <StatsBar />

          {/* Core Feature: The GenZ Intelligent LMS & Admissions CRM Ecosystem */}
          <LMSAndCRMSection onOpenPortalModal={handleOpenPortalModal} />

          {/* Welcome & Head of School Editorial Overview */}
          <WelcomeSection />

          {/* Educational Philosophy & 4 Core Pillars */}
          <PhilosophySection />

          {/* What Sets GenZ Apart (With Traveling Animated SVG Lines) */}
          <WhyUsSection />

          {/* Global Placements & Scholar Success Stories */}
          <SuccessStoriesSection />

          {/* Campus Facilities & Co-Curriculars (With Large Swinging Bell) */}
          <CampusLifeSection onOpenModal={handleOpenModal} />

          {/* Admissions Journey (Realistic iPad Held by Hands) */}
          <AdmissionsFlightPlan onOpenModal={handleOpenModal} />

          {/* Signature Curved Wave Marquee */}
          <WaveMarquee />

          {/* News, Colloquiums & Events */}
          <NewsEventsSection onOpenModal={handleOpenModal} />

          {/* High-Impact Final Call To Action */}
          <CallToAction onOpenModal={handleOpenModal} />
        </main>

        {/* Premium Global School Footer (Islamabad & Lahore Campuses, Accreditations) */}
        <Footer onOpenModal={handleOpenModal} />

        {/* Interactive Tour & Inquiry Modal */}
        <InquiryModal isOpen={modalOpen} onClose={handleCloseModal} />

        {/* Unified LMS & CRM Portal Gateway Modal */}
        <PortalModal
          isOpen={portalModalOpen}
          onClose={handleClosePortalModal}
          initialRole={portalRole}
        />

        {/* Floating Quick Action Controls */}
        <FloatingActions onOpenModal={handleOpenModal} />
      </div>
    </SmoothScroll>
  );
};

export default App;
