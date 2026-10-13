import React, { useEffect, useRef } from 'react';
import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';

interface ThreeCanvasProps {
  scrollProgress: number; // 0 to 1
}

export const ThreeCanvas: React.FC<ThreeCanvasProps> = ({ scrollProgress }) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const sceneRef = useRef<THREE.Scene | null>(null);
  const rendererRef = useRef<THREE.WebGLRenderer | null>(null);
  const globeGroupRef = useRef<THREE.Group | null>(null);
  const ringRef = useRef<THREE.Mesh | null>(null);
  const ring2Ref = useRef<THREE.Mesh | null>(null);
  const ring3Ref = useRef<THREE.Mesh | null>(null);
  const particlesRef = useRef<THREE.Points | null>(null);
  const airplaneRef = useRef<THREE.Group | null>(null);
  const leftThrustRef = useRef<THREE.Mesh | null>(null);
  const rightThrustRef = useRef<THREE.Mesh | null>(null);
  const contrailRef = useRef<THREE.Mesh | null>(null);

  useEffect(() => {
    if (!containerRef.current) return;

    const width = containerRef.current.clientWidth || window.innerWidth;
    const height = containerRef.current.clientHeight || window.innerHeight;

    // 1. Scene
    const scene = new THREE.Scene();
    sceneRef.current = scene;

    // 2. Camera with adaptive perspective for mobile & desktop
    const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
    const getTargetZ = (w: number) => (w < 480 ? 13.5 : w < 768 ? 11.5 : w < 1024 ? 9.8 : 8.5);
    camera.position.z = getTargetZ(width);
    if (width < 640) {
      camera.position.y = 0.2;
    }

    // 3. High-Performance Renderer with adaptive mobile DPR
    const isMobile = width < 768;
    const dpr = Math.min(window.devicePixelRatio || 1, isMobile ? 1.75 : 2);
    const renderer = new THREE.WebGLRenderer({
      antialias: !isMobile || dpr < 2,
      alpha: true,
      powerPreference: 'high-performance',
      stencil: false,
      depth: true,
    });
    renderer.setSize(width, height);
    renderer.setPixelRatio(dpr);
    renderer.domElement.style.width = '100%';
    renderer.domElement.style.height = '100%';
    renderer.domElement.style.display = 'block';
    containerRef.current.appendChild(renderer.domElement);
    rendererRef.current = renderer;

    // 4. Lighting - Enhanced directional & ambient illumination for 3D model
    const ambientLight = new THREE.AmbientLight(0xffffff, 1.2);
    scene.add(ambientLight);

    const dirLight = new THREE.DirectionalLight(0xffffff, 2.5);
    dirLight.position.set(8, 12, 10);
    scene.add(dirLight);

    const pointLight = new THREE.PointLight(0x0099ff, 4.0, 60);
    pointLight.position.set(6, 6, 6);
    scene.add(pointLight);

    const pointLight2 = new THREE.PointLight(0x38bdf8, 3.0, 60);
    pointLight2.position.set(-6, -6, -3);
    scene.add(pointLight2);

    // 5. Academic Globe / Orbital Group
    const globeGroup = new THREE.Group();
    scene.add(globeGroup);
    globeGroupRef.current = globeGroup;

    // Ethereal Holographic Latitude & Longitude Sphere (Crisp, futuristic, glowing cyan)
    const sphereGeo = new THREE.SphereGeometry(2.35, 22, 16);
    const sphereMat = new THREE.MeshBasicMaterial({
      color: 0x0099ff,
      wireframe: true,
      transparent: true,
      opacity: 0.38,
    });
    const sphere = new THREE.Mesh(sphereGeo, sphereMat);
    globeGroup.add(sphere);

    // Inner glowing sphere with soft cyan ambient radiance
    const innerGeo = new THREE.SphereGeometry(1.9, 32, 32);
    const innerMat = new THREE.MeshBasicMaterial({
      color: 0x00a8ff,
      transparent: true,
      opacity: 0.12,
      wireframe: false,
    });
    const innerSphere = new THREE.Mesh(innerGeo, innerMat);
    globeGroup.add(innerSphere);

    // Primary Orbital Ring (Matching logo airplane flight path)
    const ringGeo = new THREE.TorusGeometry(3.3, 0.045, 16, 120);
    const ringMat = new THREE.MeshBasicMaterial({
      color: 0x0099ff,
      transparent: true,
      opacity: 0.95,
    });
    const ring = new THREE.Mesh(ringGeo, ringMat);
    ring.rotation.x = Math.PI / 2.8;
    ring.rotation.y = Math.PI / 6;
    globeGroup.add(ring);
    ringRef.current = ring;

    // Secondary tilted ring
    const ringGeo2 = new THREE.TorusGeometry(3.8, 0.028, 16, 120);
    const ringMat2 = new THREE.MeshBasicMaterial({
      color: 0x38bdf8,
      transparent: true,
      opacity: 0.8,
    });
    const ring2 = new THREE.Mesh(ringGeo2, ringMat2);
    ring2.rotation.x = -Math.PI / 3.4;
    ring2.rotation.z = Math.PI / 4;
    globeGroup.add(ring2);
    ring2Ref.current = ring2;

    // Outer Expansive Orbital Ring (spans wide across the full header as requested)
    const ringGeo3 = new THREE.TorusGeometry(4.4, 0.02, 16, 140);
    const ringMat3 = new THREE.MeshBasicMaterial({
      color: 0x00a8ff,
      transparent: true,
      opacity: 0.6,
    });
    const ring3 = new THREE.Mesh(ringGeo3, ringMat3);
    ring3.rotation.x = Math.PI / 2.2;
    ring3.rotation.y = -Math.PI / 5;
    globeGroup.add(ring3);
    ring3Ref.current = ring3;

    // ==========================================
    // 6. Complete 3D Airliner Model & Contrail Stream
    // (Matching commercial airliner jet in reference image)
    // ==========================================
    const airplaneGroup = new THREE.Group();

    // Twin Glowing Cyan Engine Exhaust Plumes
    const jetThrustMat = new THREE.MeshBasicMaterial({
      color: 0x00f0ff,
      transparent: true,
      opacity: 0.95,
    });
    const thrustGeo = new THREE.ConeGeometry(0.018, 0.22, 12);
    thrustGeo.translate(0, -0.11, 0);

    const rightThrust = new THREE.Mesh(thrustGeo, jetThrustMat);
    rightThrust.position.set(0.13, -0.06, 0.02);
    airplaneGroup.add(rightThrust);
    rightThrustRef.current = rightThrust;

    const leftThrust = new THREE.Mesh(thrustGeo, jetThrustMat);
    leftThrust.position.set(-0.13, -0.06, -0.02);
    airplaneGroup.add(leftThrust);
    leftThrustRef.current = leftThrust;

    // Instant Procedural Model as sleek baseline / fallback
    const placeholder = new THREE.Group();
    const jetBodyMat = new THREE.MeshStandardMaterial({
      color: 0x071e3d,
      roughness: 0.28,
      metalness: 0.72,
    });
    const jetWingMat = new THREE.MeshStandardMaterial({
      color: 0x0b2545,
      roughness: 0.32,
      metalness: 0.65,
    });
    const jetCockpitMat = new THREE.MeshStandardMaterial({
      color: 0x00f0ff,
      emissive: 0x0099ff,
      emissiveIntensity: 0.85,
      roughness: 0.1,
      metalness: 0.9,
    });

    // Cabin
    const cabinGeo = new THREE.CylinderGeometry(0.046, 0.052, 0.44, 16);
    const cabin = new THREE.Mesh(cabinGeo, jetBodyMat);
    placeholder.add(cabin);

    // Nose
    const noseGeo = new THREE.ConeGeometry(0.046, 0.26, 16);
    const nose = new THREE.Mesh(noseGeo, jetBodyMat);
    nose.position.y = 0.35;
    placeholder.add(nose);

    // Cockpit
    const cockpitGeo = new THREE.SphereGeometry(0.038, 12, 12);
    const cockpit = new THREE.Mesh(cockpitGeo, jetCockpitMat);
    cockpit.position.set(0.024, 0.22, 0);
    cockpit.scale.set(0.7, 1.5, 0.8);
    placeholder.add(cockpit);

    // Tail Cone
    const tailConeGeo = new THREE.CylinderGeometry(0.052, 0.016, 0.3, 16);
    const tailCone = new THREE.Mesh(tailConeGeo, jetBodyMat);
    tailCone.position.y = -0.37;
    placeholder.add(tailCone);

    // Wings
    const wingShape = new THREE.Shape();
    wingShape.moveTo(0, 0.14);
    wingShape.lineTo(0.58, -0.16);
    wingShape.lineTo(0.55, -0.25);
    wingShape.lineTo(0.05, -0.09);
    wingShape.lineTo(-0.05, -0.09);
    wingShape.lineTo(-0.55, -0.25);
    wingShape.lineTo(-0.58, -0.16);
    wingShape.closePath();
    const wingGeo = new THREE.ExtrudeGeometry(wingShape, {
      depth: 0.016,
      bevelEnabled: true,
      bevelSize: 0.004,
      bevelThickness: 0.004,
      bevelSegments: 2,
    });
    wingGeo.center();
    const wings = new THREE.Mesh(wingGeo, jetWingMat);
    placeholder.add(wings);

    // Tail Fin
    const finShape = new THREE.Shape();
    finShape.moveTo(0, 0);
    finShape.lineTo(0.24, -0.22);
    finShape.lineTo(0.21, -0.29);
    finShape.lineTo(0, -0.24);
    finShape.closePath();
    const finGeo = new THREE.ExtrudeGeometry(finShape, {
      depth: 0.012,
      bevelEnabled: true,
      bevelSize: 0.003,
      bevelThickness: 0.003,
      bevelSegments: 2,
    });
    finGeo.center();
    const fin = new THREE.Mesh(finGeo, jetWingMat);
    fin.position.set(0.11, -0.38, 0);
    placeholder.add(fin);

    // Horizontal Elevators
    const hTailShape = new THREE.Shape();
    hTailShape.moveTo(0, -0.34);
    hTailShape.lineTo(0.24, -0.46);
    hTailShape.lineTo(0.22, -0.51);
    hTailShape.lineTo(0, -0.48);
    hTailShape.lineTo(-0.22, -0.51);
    hTailShape.lineTo(-0.24, -0.46);
    hTailShape.closePath();
    const hTailGeo = new THREE.ExtrudeGeometry(hTailShape, {
      depth: 0.01,
      bevelEnabled: true,
      bevelSize: 0.002,
      bevelThickness: 0.002,
      bevelSegments: 2,
    });
    hTailGeo.center();
    const hTail = new THREE.Mesh(hTailGeo, jetWingMat);
    hTail.position.set(0.01, 0, 0);
    placeholder.add(hTail);

    airplaneGroup.add(placeholder);

    // Load Complete 3D Airliner Model (.glb)
    const gltfLoader = new GLTFLoader();
    gltfLoader.load(
      '/models/airplane.glb',
      (gltf) => {
        const model = gltf.scene;
        // Proportion matching orbital flight ring
        model.scale.set(0.024, 0.024, 0.024);
        
        // Orient airliner along flight trajectory:
        // Heading forward along +Y, banking towards camera for dramatic 3D view
        model.rotation.set(0, Math.PI / 2.3, Math.PI / 2, 'YXZ');
        model.position.set(0, 0, 0);

        // Enhance metallic PBR materials for realistic aircraft reflections
        model.traverse((child) => {
          if ((child as THREE.Mesh).isMesh) {
            const mesh = child as THREE.Mesh;
            mesh.castShadow = true;
            mesh.receiveShadow = true;
            if (mesh.material) {
              const mat = mesh.material as THREE.MeshStandardMaterial;
              mat.roughness = 0.28;
              mat.metalness = 0.55;
              mat.needsUpdate = true;
            }
          }
        });

        // Remove placeholder and mount loaded 3D model
        airplaneGroup.remove(placeholder);
        airplaneGroup.add(model);
      },
      undefined,
      (err) => {
        console.warn('Using procedural fallback for airliner model:', err);
      }
    );

    // Mount airplane on primary orbital ring at x = 3.3
    airplaneGroup.position.set(3.3, 0, 0);
    airplaneGroup.rotation.y = -0.32;
    ring.add(airplaneGroup);
    airplaneRef.current = airplaneGroup;

    // I. Swooping Jet Contrail / Vapor Stream (Matching reference image)
    const contrailGeo = new THREE.TorusGeometry(3.3, 0.042, 8, 54, -Math.PI / 3.4);
    const contrailMat = new THREE.MeshBasicMaterial({
      color: 0x00d2ff,
      transparent: true,
      opacity: 0.8,
    });
    const contrail = new THREE.Mesh(contrailGeo, contrailMat);
    ring.add(contrail);
    contrailRef.current = contrail;

    // Starfield / Knowledge Particles
    const particlesCount = 220;
    const posArray = new Float32Array(particlesCount * 3);
    for (let i = 0; i < particlesCount * 3; i++) {
      posArray[i] = (Math.random() - 0.5) * 16;
    }
    const particlesGeo = new THREE.BufferGeometry();
    particlesGeo.setAttribute('position', new THREE.BufferAttribute(posArray, 3));
    const particlesMat = new THREE.PointsMaterial({
      size: 0.045,
      color: 0x0099ff,
      transparent: true,
      opacity: 0.65,
    });
    const particles = new THREE.Points(particlesGeo, particlesMat);
    scene.add(particles);
    particlesRef.current = particles;

    // Animation Loop with Visibility Caching
    let animationFrameId: number = 0;
    let isVisible = true;

    const animate = () => {
      if (!isVisible) {
        animationFrameId = 0;
        return;
      }
      animationFrameId = requestAnimationFrame(animate);

      const time = performance.now() * 0.001;

      // Ambient continuous idle rotation
      if (globeGroupRef.current) {
        globeGroupRef.current.rotation.y += 0.0025;
      }
      if (ringRef.current) {
        ringRef.current.rotation.z += 0.008;
      }
      if (ring2Ref.current) {
        ring2Ref.current.rotation.z -= 0.005;
      }
      if (ring3Ref.current) {
        ring3Ref.current.rotation.z += 0.004;
      }
      if (particlesRef.current) {
        particlesRef.current.rotation.y -= 0.0005;
      }

      // Airplane Dynamic Flight Dynamics (Banking & Pitching)
      if (airplaneRef.current) {
        // Dynamic banking roll into the flight curve
        airplaneRef.current.rotation.y = -0.32 + Math.sin(time * 2.2) * 0.06;
        // Subtle aerodynamic pitch oscillation
        airplaneRef.current.rotation.z = Math.sin(time * 1.6) * 0.03;
      }

      // Dynamic Cyan Engine Thrust Plumes Pulse
      if (leftThrustRef.current && rightThrustRef.current) {
        const thrustScale = 1.0 + Math.sin(time * 14) * 0.35;
        leftThrustRef.current.scale.set(1, thrustScale, 1);
        rightThrustRef.current.scale.set(1, thrustScale, 1);
      }

      // Swooping Vapor Contrail Shimmer
      if (contrailRef.current) {
        const contrailMat = contrailRef.current.material as THREE.MeshBasicMaterial;
        if (contrailMat) {
          contrailMat.opacity = 0.72 + Math.sin(time * 3.5) * 0.16;
        }
      }

      renderer.render(scene, camera);
    };

    // IntersectionObserver to pause rendering when offscreen
    let observer: IntersectionObserver | null = null;
    if (typeof IntersectionObserver !== 'undefined' && containerRef.current) {
      observer = new IntersectionObserver(
        ([entry]) => {
          isVisible = entry.isIntersecting;
          if (isVisible && !animationFrameId) {
            animate();
          }
        },
        { rootMargin: '120px' }
      );
      observer.observe(containerRef.current);
    }

    animate();

    // Resize Handler
    const handleResize = () => {
      if (!containerRef.current) return;
      const newWidth = containerRef.current.clientWidth || window.innerWidth;
      const newHeight = containerRef.current.clientHeight || window.innerHeight;
      camera.aspect = newWidth / newHeight;
      camera.position.z = getTargetZ(newWidth);
      if (newWidth < 640) {
        camera.position.y = 0.2;
      } else {
        camera.position.y = 0;
      }
      camera.updateProjectionMatrix();
      renderer.setSize(newWidth, newHeight);
    };
    window.addEventListener('resize', handleResize);

    return () => {
      if (observer) observer.disconnect();
      window.removeEventListener('resize', handleResize);
      if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
      }
      if (renderer.domElement && renderer.domElement.parentNode) {
        renderer.domElement.parentNode.removeChild(renderer.domElement);
      }
      renderer.dispose();
    };
  }, []);

  // Update 3D rotation and scale based on scroll progress
  useEffect(() => {
    if (!globeGroupRef.current) return;

    const targetRotY = scrollProgress * Math.PI * 4;
    const targetRotX = (scrollProgress - 0.5) * 0.7;
    const scale = 1 + Math.sin(scrollProgress * Math.PI) * 0.2;

    globeGroupRef.current.rotation.y = targetRotY;
    globeGroupRef.current.rotation.x = targetRotX;
    globeGroupRef.current.scale.set(scale, scale, scale);
  }, [scrollProgress]);

  return <div ref={containerRef} className="w-full h-full pointer-events-none" />;
};
