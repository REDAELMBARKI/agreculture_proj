import React, { useMemo, useRef, useState, useEffect } from "react";
import { useLocation, useNavigate, useParams } from "react-router-dom";
import api from "../../../services/api";
import ziggyRoute from "../../../utils/route";
import {
  Palette,
  Ruler,
  Shapes,
  Tag,
  Plus,
  X,
  ChevronRight,
  Leaf,
  Tractor,
  Sprout,
  Map,
  Handshake,
  FlaskConical as Flask,
  Droplets as Water,
  CheckCircle,
  Wheat,
  Beef as Cow,
  Scale,
} from "lucide-react";
import {
  MapPoint as MapPin,
  Box as Package,
  Delivery as Truck,
} from "@solar-icons/react";
import {
  TextField,
  Button,
  CircularProgress,
  Box,
  Typography,
  Container,
  Grid,
  Paper,
  InputAdornment,
  IconButton,
  Chip,
  Divider,
  Alert,
  FormControl,
  OutlinedInput,
  FormHelperText,
  InputLabel,
  Snackbar,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
} from "@mui/material";
import AddPhotoAlternateIcon from "@mui/icons-material/AddPhotoAlternate";
import CloseIcon from "@mui/icons-material/Close";
import CustomSelect from "../Common/CustomSelect";
import {
  IconCardButton,
  PillButton,
  Stepper,
} from "./announcement/Shared";
import "../../../css/add_announcement.css";

// Sub-categories data
const SUB_CATEGORIES_MAP: Record<string, string[]> = {
  "Crops": ["Cereals", "Fruits", "Vegetables", "Legumes"],
  "Livestock": ["Cattle", "Poultry", "Sheep & Goats", "Honeybees"],
  "Seeds": ["Crop Seeds", "Vegetable Seeds", "Fruit Seeds"],
  "Equipment": ["Tractors", "Harvesters", "Plows", "Tools"],
  "Land": ["Farm Land", "Orchards", "Grazing Land"],
  "Services": ["Consulting", "Labor", "Transportation"],
  "Fertilizers": ["Organic Fertilizers", "Chemical Fertilizers", "Pesticides"],
  "Irrigation": ["Drip Systems", "Sprinklers", "Pumps"],
  "Organic": ["Organic Produce", "Eco-friendly Supplies"]
};

// Color mapping for French names to Hex
const COLOR_MAP: Record<string, string> = {
  "Noir": "#000000",
  "Blanc": "#FFFFFF",
  "Gris": "#808080",
  "Rouge": "#FF0000",
  "Bleu": "#0000FF",
  "Vert": "#008000",
  "Jaune": "#FFFF00",
  "Rose": "#FFC0CB",
  "Violet": "#800080",
  "Orange": "#FFA500",
  "Marron": "#A52A2A",
  "Beige": "#F5F5DC",
  "Marine": "#000080",
  "Ciel": "#87CEEB",
  "Doré": "#FFD700",
  "Argenté": "#C0C0C0",
  "Multicolore": "linear-gradient(45deg, red, blue, green, yellow)"
};

// Types
interface Category {
  id: number;
  name: string;
  slug: string;
  icon: string;
  children: Category[];
}

interface FormState {
  super_category_id: number | null;
  super_category_name: string | null;
  sub_category_names: string[];
  sub_category_ids: number[];
  title: string;
  description: string;
  listing_type: "single" | "collection";
  listing_mode: "sell" | "donate";
  price: string;
  currency: string;
  price_negotiable: boolean;
  condition: string;
  material: string;
  quantity: string;
  quantity_unit: string;
  region: string;
  brand: string;
  season: string;
  sizes: string[];
  colors: string[];
  city_id: string;
  handover_method: string;
  pickup_address: string;
  contact_phone: string;
  custom_colors: { name: string; hex: string }[];
}

interface User {
  id?: number;
  slug?: string;
  name?: string;
  role?: string;
}

// Helper to get icon by category name
const getCategoryIcon = (iconName: string): any => {
  const iconMap: Record<string, any> = {
    'leaf': Leaf,
    'cow': Cow,
    'sprout': Sprout,
    'tractor': Tractor,
    'map': Map,
    'handshake': Handshake,
    'flask': Flask,
    'water': Water,
    'check-circle': CheckCircle,
    'wheat': Wheat,
    'package': Package,
  };
  return iconMap[iconName] || Package;
};

// Types for field errors and status messages
interface FieldErrors {
  [key: string]: string;
}

interface StatusMessage {
  type: "success" | "error";
  message: string;
}

interface UploadSlot {
  status: 'idle' | 'uploading' | 'done' | 'error';
  url: string | null;
  id: number | null;
}

const BASE_STEPS = [
  { key: "category", label: "Catégorie" },
  { key: "product", label: "Produit & Média" },
  { key: "variants", label: "Spécifications" },
  { key: "price", label: "Prix" },
  { key: "location", label: "Localisation" },
];

// Fallback categories while loading
const FALLBACK_CATEGORIES = [
  { id: 1001, name: "Crops", icon: Leaf },
  { id: 1002, name: "Livestock", icon: Cow },
  { id: 1003, name: "Seeds", icon: Sprout },
  { id: 1004, name: "Equipment", icon: Tractor },
  { id: 1005, name: "Land", icon: Map },
  { id: 1006, name: "Organic", icon: CheckCircle },
];

interface FilterAttributes {
  regions: string[];
  quantityUnits: string[];
  harvestSeasons: string[];
  soilTypes: string[];
  conditions: { label: string; value: string }[];
  listingTypes: string[];
  colors: string[];
}

interface Product {
  id: number;
  slug: string;
  user?: {
    id: number;
    slug: string;
    name: string;
  };
  super_category_id: number;
  super_category_name?: string;
  sub_category_names?: string[];
  title: string;
  description: string;
  listing_type: "single" | "collection";
  listing_mode: "sell" | "donate";
  price: string | number;
  currency: string;
  price_negotiable: boolean;
  condition: string;
  gender: string;
  age_range: string;
  brand?: string;
  season?: string;
  sizes?: string[];
  colors?: string[];
  city: string;
  pickup_address: string;
  contact_phone: string;
  handover_method: string;
  thumbnail?: { url: string; id: number };
  gallery?: { url: string; id: number }[];
}

interface AddAnnouncementProps {
  product?: Product;
}

export default function Add_Announcement({ product: propProduct }: AddAnnouncementProps) {
  const navigate = useNavigate();
  const location = useLocation();
  const { userSlug, announcementSlug } = useParams();
  const [product, setProduct] = useState<Product | undefined>(propProduct || location.state?.product);
  const isEditMode = !!product || (!!userSlug && !!announcementSlug);
  const user: User = JSON.parse(localStorage.getItem("user") || "{}");
  const token = localStorage.getItem("token");
  const isAuthenticated = !!token && !!user?.id;
  const [authDialogOpen, setAuthDialogOpen] = useState<boolean>(!isAuthenticated);
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Fetch product if slugs are present and no product in state
  useEffect(() => {
    if (userSlug && announcementSlug && !product) {
      const fetchProduct = async () => {
        try {
        const response = await api.get(ziggyRoute('announcements.show', { 
          announcement: announcementSlug 
        }));
        if (response.data.status === "success") {
            setProduct(response.data.product);
          }
        } catch (error) {
          console.error("Failed to fetch product by slug:", error);
          setStatus({ type: 'error', message: "Impossible de charger l'annonce." });
        }
      };
      fetchProduct();
    }
  }, [userSlug, announcementSlug, product]);

  const [stepKey, setStepKey] = useState<string>("category");
  const [status, setStatus] = useState<StatusMessage | null>(null);
  const [toastOpen, setToastOpen] = useState(false);
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});
  const [uploadSlots, setUploadSlots] = useState<UploadSlot[]>(
    Array(8).fill(null).map(() => ({ status: 'idle', url: null, id: null }))
  );
  const [mainPhotoIndex, setMainPhotoIndex] = useState<number>(0);
  const isUploading = useMemo(() => uploadSlots.some(s => s.status === 'uploading'), [uploadSlots]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [attributes, setAttributes] = useState<FilterAttributes>({
    cities: [],
    ageRanges: [],
    clothingSizes: [],
    shoeSizes: [],
    conditions: [],
    listingTypes: [],
    materials: [],
    colors: []
  });
  const [loading, setLoading] = useState<boolean>(true);

  const [form, setForm] = useState<FormState>({
    super_category_id: null,
    super_category_name: null,
    sub_category_names: [],
    sub_category_ids: [],    
    title: "",
    description: "",
    listing_type: "single",
    quantity: "",
    quantity_unit: "kg",
    region: "",
    brand: "",
    condition: "",
    sizes: [],
    colors: [],
    season: "",
    material: "",
    listing_mode: "sell",
    price: "",
    currency: "MAD",
    price_negotiable: false,
    city_id: "",
    pickup_address: "",
    contact_phone: "+212",
    handover_method: "both",
    custom_colors: [],
  });

  const [tempColorName, setTempColorName] = useState("");
  const [tempColorHex, setTempColorHex] = useState("#3b82f6");

  // Fetch initial data and pre-fill if in edit mode
  useEffect(() => {
    const fetchInitData = async () => {
      try {
        const response = await api.get(ziggyRoute('marketplace.init-data'));
        if (response.data.status === "success") {
          const processedCategories = (response.data.categories || []).map((cat: any) => ({
            ...cat,
            id: Number(cat.id),
            children: (cat.children || []).map((child: any) => ({
              ...child,
              id: Number(child.id)
            }))
          }));
          setCategories(processedCategories);
          setAttributes({
            regions: response.data.regions || [],
            quantityUnits: response.data.quantityUnits || [],
            harvestSeasons: response.data.harvestSeasons || [],
            soilTypes: response.data.soilTypes || [],
            conditions: response.data.conditions || [],
            listingTypes: response.data.listingTypes || [],
            colors: response.data.colors || []
          });

          // Pre-fill form if in edit mode
          if (isEditMode && product) {
            setForm({
              super_category_id: product.super_category_id,
              super_category_name: product.super_category_name || null,
              sub_category_names: product.sub_category_names || [],
              sub_category_ids: [], // Backend might need this but we use names for now
              title: product.title,
              description: product.description,
              listing_type: product.listing_type,
              gender: product.gender,
              age_range: product.age_range,
              brand: product.brand || "",
              condition: product.condition,
              sizes: product.sizes || [],
              colors: product.colors || [],
              season: product.season || "",
              material: "", // Missing in product?
              listing_mode: "sell",
              price: String(product.price),
              currency: product.currency,
              price_negotiable: product.price_negotiable,
              city_id: String(product.city_id || ""),
              pickup_address: product.pickup_address,
              contact_phone: product.contact_phone || (product as any).phone_contact,
              handover_method: product.handover_method,
              custom_colors: [], // Assuming custom colors aren't separate in backend yet
            });

            // Set upload slots
            const slots: UploadSlot[] = Array(8).fill(null).map(() => ({ status: 'idle', url: null, id: null }));
            if (product.thumbnail) {
              slots[0] = { status: 'done', url: product.thumbnail.url, id: product.thumbnail.id };
            }
            if (product.gallery) {
              product.gallery.forEach((media, idx) => {
                if (idx + 1 < slots.length) {
                  slots[idx + 1] = { status: 'done', url: media.url, id: media.id };
                }
              });
            }
            setUploadSlots(slots);
          }
        }
      } catch (error) {
        console.error("Failed to fetch initial data:", error);
      } finally {
        setLoading(false);
      }
    };
    fetchInitData();
  }, [isEditMode, product]);

  const visibleSteps = useMemo(() => BASE_STEPS, []);

  const stepIndex = visibleSteps.findIndex((step) => step.key === stepKey);
  const currentStepNumber = stepIndex + 1;
  const isLastStep = currentStepNumber === visibleSteps.length;

  const selectedCategory = useMemo(() => {
    const allCats = categories.length > 0 ? categories : FALLBACK_CATEGORIES;
    let found = allCats.find(c => c.id === form.super_category_id);
    
    if (!found && form.super_category_name) {
      found = allCats.find(c => c.name === form.super_category_name);
    }
    return found;
  }, [form.super_category_id, form.super_category_name, categories]);

  // Sync super_category_id when API data loads and matches by name
  useEffect(() => {
    if (categories.length > 0 && selectedCategory && selectedCategory.id !== form.super_category_id) {
      // Check if this selectedCategory is actually from the API list
      if (categories.some(c => c.id === selectedCategory.id)) {
        updateField("super_category_id", selectedCategory.id);
      }
    }
  }, [categories, selectedCategory, form.super_category_id]);

  const subcategoryOptions = useMemo(() => {
    if (!selectedCategory) return [];
    
    // 1. Check if category has children from API
    if (selectedCategory.children && selectedCategory.children.length > 0) {
      return selectedCategory.children.map((child: any) => ({
        id: child.id,
        label: child.name,
        value: child.name,
        icon: <Shapes size={16} />
      }));
    }
    
    // 2. Fallback to hardcoded map using the name
    const fallbackNames = SUB_CATEGORIES_MAP[selectedCategory.name] || [];
    return fallbackNames.map((name, index) => ({
      id: `${selectedCategory.id}-${index}`,
      label: name,
      value: name,
      icon: <Shapes size={16} />
    }));
  }, [selectedCategory]);

  const updateField = (key: keyof FormState, value: any) => setForm((prev) => ({ ...prev, [key]: value }));
  
  const handleCategorySelect = (id: number, name: string) => {
    setForm(prev => ({
      ...prev,
      super_category_id: id,
      super_category_name: name,
      sub_category_names: [], // Reset sub-categories when main category changes
      sub_category_ids: []
    }));
    clearFieldError('super_category_id');
  };

  const handleSubCategoryChange = (selectedNames: string[]) => {
    // Find IDs from subcategoryOptions
    const selectedIds = selectedNames.map(name => {
      const option = subcategoryOptions.find(opt => opt.value === name);
      const idNum = Number(option?.id);
      return isNaN(idNum) ? null : idNum;
    }).filter(id => id !== null) as number[];

    setForm(prev => ({
      ...prev,
      sub_category_names: selectedNames,
      sub_category_ids: selectedIds
    }));
    clearFieldError('sub_category_names');
  };

  const clearFieldError = (key: string) =>
    setFieldErrors((prev) => {
      if (!prev[key]) return prev;
      const next = { ...prev };
      delete next[key];
      return next;
    });

  const validateStep = (targetStepKey = stepKey) => {
    const errors: FieldErrors = {};
    if (targetStepKey === "category") {
      if (!form.super_category_id) errors.super_category_id = "Choisissez une catégorie principale.";
      if (form.sub_category_names.length === 0) errors.sub_category_names = "Choisissez une sous-catégorie.";
    }
    if (targetStepKey === "product") {
      if (!form.title.trim()) errors.title = "Le titre est obligatoire.";
      if (!form.description.trim()) errors.description = "La description est obligatoire.";
      if (!form.condition) errors.condition = "Choisissez l'état du produit.";
      if (!uploadSlots.some(s => s.status === 'done')) errors.photos = "Ajoutez au moins une photo.";
    }
    if (targetStepKey === "variants" && form.listing_type === "single") {
      if (!form.season) errors.season = "Choisissez une saison.";
    }
    if (targetStepKey === "price" && form.listing_mode === "sell" && !String(form.price).trim()) {
      errors.price = "Le prix est obligatoire pour une vente.";
    }
    if (targetStepKey === "location") {
      if (!form.handover_method) errors.handover_method = "Choisissez un mode de remise.";
      if (!form.city_id) errors.city_id = "Choisissez une ville.";
      if (!form.pickup_address.trim()) errors.pickup_address = "L'adresse est obligatoire.";
      if (!form.contact_phone.trim() || form.contact_phone === "+212") {
        errors.contact_phone = "Le numéro de téléphone est obligatoire.";
      } else if (!/^\+212[5-7]\d{8}$/.test(form.contact_phone)) {
        errors.contact_phone = "Format: 9 chiffres après +212, commençant par 5, 6 ou 7.";
      }
    }
    return errors;
  };

  const goNext = () => {
    const current = visibleSteps[stepIndex];
    const errors = validateStep(current?.key);
    if (Object.keys(errors).length) {
      setFieldErrors(errors);
      setStatus({ type: "error", message: "Veuillez corriger les erreurs en rouge." });
      return;
    }
    setFieldErrors({});
    setStatus(null);
    const nextKey = visibleSteps[stepIndex + 1]?.key;
    if (nextKey) {
      setStepKey(nextKey);
    }
  };

  const goPrev = () => {
    setStatus(null);
    setFieldErrors({});
    const previousKey = visibleSteps[stepIndex - 1]?.key;
    if (previousKey) {
      setStepKey(previousKey);
    }
  };

  const handleUpload = async (index: number, file: File) => {
    if (!isAuthenticated) {
      setAuthDialogOpen(true);
      setStatus({ type: "error", message: "Connectez-vous pour ajouter des photos." });
      return;
    }

    setUploadSlots(prev => {
      const next = [...prev];
      next[index] = { ...next[index], status: 'uploading' };
      return next;
    });

    try {
      const formData = new FormData();
      formData.append('image', file);
      formData.append('mediable_type', 'product');
      formData.append('collection', index === 0 ? 'thumbnail' : 'gallery');

      const response = await api.post(ziggyRoute('media.upload'), formData, {
        headers: {
          Accept: 'application/json',
        },
      });

      const data = response.data;
      const isSuccess = data?.status === 'success' || data?.success === true;
      const url = data?.url ?? data?.media?.url ?? data?.data?.url ?? null;
      const mediaId = data?.mediaId ?? data?.media?.id ?? data?.data?.mediaId ?? data?.data?.id ?? null;

      if (isSuccess && url && mediaId) {
        setUploadSlots(prev => {
          const next = [...prev];
          next[index] = { status: 'done', url, id: mediaId };
          return next;
        });
        clearFieldError('photos');
      } else {
        throw new Error(data?.message || 'Upload failed');
      }
    } catch (error: any) {
      const errorMessage = error.response?.data?.errors 
        ? Object.values(error.response.data.errors).flat().join(', ') 
        : (error.response?.data?.message || error.message || 'Upload failed');
      
      console.error('Full upload error details:', error.response?.data || error);
      setStatus({ type: 'error', message: errorMessage });
      
      setUploadSlots(prev => {
        const next = [...prev];
        next[index] = { ...next[index], status: 'error' };
        return next;
      });
    }
  };

  const onPhotoChange = async (event: React.ChangeEvent<HTMLInputElement>, slotIndex?: number) => {
    const files = Array.from(event.target.files || []);
    if (files.length === 0) return;

    if (slotIndex !== undefined) {
      await handleUpload(slotIndex, files[0]);
    } else {
      let currentFileIndex = 0;
      for (let i = 0; i < uploadSlots.length && currentFileIndex < files.length; i++) {
        if (uploadSlots[i].status === 'idle' || uploadSlots[i].status === 'error') {
          await handleUpload(i, files[currentFileIndex]);
          currentFileIndex++;
        }
      }
    }
    event.target.value = '';
  };

  const removePhoto = async (indexToRemove: number) => {
    const slot = uploadSlots[indexToRemove];
    if (slot.id) {
      try {
        await api.delete(ziggyRoute('media.delete-temporary', { mediaId: slot.id }));
      } catch (error) {
        console.error('Failed to delete temporary media:', error);
      }
    }
    
    setUploadSlots(prev => {
      const next = [...prev];
      next[indexToRemove] = { status: 'idle', url: null, id: null };
      return next;
    });
  };

  const submitAnnouncement = async () => {
    console.log('=== SUBMIT ANNOUNCEMENT START ===');
    if (!user?.id) {
      setAuthDialogOpen(true);
      setStatus({ type: "error", message: "Connectez-vous d'abord." });
      return;
    }

    console.log('Current stepKey:', stepKey);
    console.log('All form data:', form);
    console.log('Upload slots:', uploadSlots);
    
    const submitErrors = validateStep("location");
    console.log('=== SUBMIT ERRORS ===');
    console.log(submitErrors);
    console.log('=== FIELD ERRORS ===');
    console.log(fieldErrors);
    if (Object.keys(submitErrors).length) {
      setFieldErrors(submitErrors);
      setStatus({ type: "error", message: "Veuillez corriger les erreurs avant publication." });
      return;
    }

    const mediaIds = uploadSlots.filter(s => s.id).map(s => s.id);
    console.log('Media IDs:', mediaIds);
    if (mediaIds.length === 0) {
      setStatus({ type: 'error', message: 'Veuillez ajouter au moins une photo.' });
      return;
    }

    const payload = {
      ...form,
      user_id: user.id,
      city_id: form.city_id,
      price: parseFloat(form.price) || 0,
      currency: "MAD",
      media_ids: mediaIds,
    };
    
    console.log('=== PAYLOAD TO SEND ===');
    console.log(payload);
    console.log('=== FORM:', form);

    try {
      let response;
      console.log('Is edit mode?', isEditMode);
      
      if (isEditMode && product) {
        console.log('PUT request to update', product.slug);
        response = await api.put(ziggyRoute('announcements.update', { 
          announcement: product.slug 
        }), payload);
      } else {
        console.log('POST request to store');
        response = await api.post(ziggyRoute('announcements.store'), payload);
      }

      console.log('=== RESPONSE ===');
      console.log(response);
      console.log('=== RESPONSE DATA ===');
      console.log(response.data);

      if (response.data.status === "success") {
        setStatus({ type: "success", message: isEditMode ? "Annonce mise à jour avec succès." : "Annonce publiée avec succès." });
        setToastOpen(true);
        const targetSlug = response.data.product?.slug || product?.slug;
        setTimeout(() => {
          if (targetSlug) {
            navigate(`/announcements/${targetSlug}`);
          } else {
            navigate("/user_dashboard");
          }
        }, 1200);
        return;
      }
      setStatus({ type: "error", message: response.data.message || "Erreur de validation." });
    } catch (error: any) {
      console.error('=== ERROR IN SUBMIT ===');
      console.error('Error object:', error);
      console.error('Error response data:', error.response?.data);
      
      const errorMessage = error.response?.data?.errors 
        ? Object.values(error.response.data.errors).flat().join(', ') 
        : (error.response?.data?.message || "Erreur réseau.");
      setStatus({ type: "error", message: errorMessage });
    }
  };

  const renderStep = () => {
    switch (stepKey) {
      case "category":
        return (
          <Box>
            <Typography variant="h6" sx={{ mb: 3, fontWeight: 600 }}>
              Choisissez la catégorie
            </Typography>
            <Grid container spacing={2}>
              {(categories.length > 0 ? categories : FALLBACK_CATEGORIES).map((cat: any) => {
                const isFromApi = categories.length > 0;
                const Icon = isFromApi ? getCategoryIcon(cat.icon) : cat.icon;
                const label = cat.name;
                const id = cat.id;
                const isActive = form.super_category_id === id;

                return (
                  <Grid item xs={12} sm={4} key={id}>
                    <IconCardButton
                      icon={Icon}
                      title={label}
                      active={isActive}
                      onClick={() => handleCategorySelect(id, label)}
                    />
                  </Grid>
                );
              })}
            </Grid>
            {fieldErrors.super_category_id && (
              <Typography color="error" variant="caption" sx={{ mt: 1, display: 'block' }}>
                {fieldErrors.super_category_id}
              </Typography>
            )}

            {form.super_category_id && (
              <Box className="aa-subcategories-container" sx={{ mt: 6 }}>
                <Box sx={{ display: 'flex', alignItems: 'center', gap: 1.5, mb: 3 }}>
                  <Box sx={{ width: 4, height: 24, bgcolor: '#3b82f6', borderRadius: 1 }} />
                  <Typography variant="h6" sx={{ fontWeight: 700, color: '#1e293b' }}>
                    Choisissez les sous-catégories
                  </Typography>
                </Box>
                
                <CustomSelect
                  label="Sélectionner des sous-catégories"
                  multiple={true}
                  options={subcategoryOptions}
                  value={form.sub_category_names}
                  onChange={(val) => handleSubCategoryChange(val as string[])}
                  error={!!fieldErrors.sub_category_names}
                  helperText={fieldErrors.sub_category_names}
                />
              </Box>
            )}
          </Box>
        );

      case "product":
        const uploadedCount = uploadSlots.filter(s => s.status !== 'idle').length;
        const firstIdleIndex = uploadSlots.findIndex(s => s.status === 'idle');

        return (
          <Box>
            <Typography variant="h6" sx={{ mb: 3, fontWeight: 600 }}>
              Détails du produit
            </Typography>
            
            {/* Row 1: Titre and Marque - Equal-width inputs side by side taking the LEFT half of the row. */}
            <Box sx={{ mb: 4, width: '100%' }}>
              <Grid container spacing={3}>
                <Grid item xs={12} md={6}>
                  <Box sx={{ display: 'flex', gap: 2 }}>
                    <TextField
                      fullWidth
                      label="Titre de l'annonce"
                      placeholder="Ex: Poussette"
                      value={form.title}
                      onChange={(e) => updateField("title", e.target.value)}
                      error={!!fieldErrors.title}
                      helperText={fieldErrors.title}
                    />
                    <TextField
                      fullWidth
                      label="Marque (Optionnel)"
                      value={form.brand}
                      onChange={(e) => updateField("brand", e.target.value)}
                      placeholder="Ex: Cybex"
                    />
                  </Box>
                </Grid>
              </Grid>
            </Box>

            {/* Row 2: Description - Full-width textarea alone on its own row */}
            <Box sx={{ mb: 4, width: '100%' }}>
              <TextField
                fullWidth
                multiline
                rows={8}
                label="Description"
                placeholder="Décrivez votre produit (état, marque, défauts éventuels...)"
                value={form.description}
                onChange={(e) => updateField("description", e.target.value)}
                error={!!fieldErrors.description}
                helperText={fieldErrors.description}
              />
            </Box>

            {/* Row 3: État du produit - Full-width dropdown alone on its own row */}
            <Box sx={{ mb: 4, width: '100%' }}>
                <CustomSelect
                  label="État du produit"
                  options={attributes.conditions}
                  value={form.condition}
                  onChange={(val) => {
                    const selected = attributes.conditions.find(o => (o.value || o.id) === val);
                    updateField("condition", selected?.value || val);
                  }}
                  error={!!fieldErrors.condition}
                  helperText={fieldErrors.condition}
                />
            </Box>

            {/* Row 4: Photos - Single large dashed-border upload box full width. One empty slot at a time. */}
            <Box sx={{ mb: 4, width: '100%' }}>
              <Typography variant="subtitle2" sx={{ mb: 2, fontWeight: 600 }}>
                Photos
              </Typography>
              <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 2 }}>
                {uploadSlots.map((slot, index) => {
                  if (slot.status === 'idle') return null;
                  return (
                    <Box 
                      key={index} 
                      sx={{ 
                        position: 'relative', 
                        width: 110, 
                        height: 110,
                        borderRadius: 2,
                        border: '1px solid #e2e8f0',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        overflow: 'hidden',
                        bgcolor: '#f8fafc',
                      }}
                    >
                      {slot.status === 'uploading' && <CircularProgress size={32} />}
                      {slot.status === 'done' && slot.url && (
                        <>
                          <img src={slot.url} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                          <IconButton 
                            size="small" 
                            onClick={(e) => { e.stopPropagation(); removePhoto(index); }}
                            sx={{ 
                              position: 'absolute', 
                              top: 4, 
                              right: 4, 
                              bgcolor: 'rgba(255,255,255,0.9)',
                              padding: '2px',
                              '&:hover': { bgcolor: 'white' }
                            }}
                          >
                            <CloseIcon sx={{ fontSize: 16 }} />
                          </IconButton>
                          {index === 0 && (
                            <Box sx={{ 
                              position: 'absolute', 
                              bottom: 0, 
                              left: 0, 
                              right: 0, 
                              bgcolor: 'rgba(59, 130, 246, 0.8)', 
                              color: 'white', 
                              fontSize: '0.65rem', 
                              textAlign: 'center',
                              py: 0.5,
                              fontWeight: 600
                            }}>
                              Principale
                            </Box>
                          )}
                        </>
                      )}
                      {slot.status === 'error' && (
                        <Box sx={{ p: 1, textAlign: 'center' }}>
                          <Typography variant="caption" color="error">Échec</Typography>
                          <IconButton size="small" onClick={() => removePhoto(index)}><X size={14} /></IconButton>
                        </Box>
                      )}
                    </Box>
                  );
                })}

                {firstIdleIndex !== -1 && (
                  <Box 
                    sx={{ 
                      width: uploadedCount === 0 ? '100%' : 110, 
                      height: uploadedCount === 0 ? 200 : 110,
                      borderRadius: 3,
                      border: '2px dashed #cbd5e1',
                      display: 'flex',
                      flexDirection: 'column',
                      alignItems: 'center',
                      justifyContent: 'center',
                      bgcolor: '#f8fafc',
                      cursor: 'pointer',
                      transition: 'all 0.2s',
                      '&:hover': { borderColor: '#3b82f6', bgcolor: '#eff6ff' }
                    }}
                    onClick={() => {
                      const input = document.createElement('input');
                      input.type = 'file';
                      input.accept = 'image/*';
                      input.style.display = 'none';
                      document.body.appendChild(input);
                      input.onchange = (e) => {
                        onPhotoChange(e as any, firstIdleIndex);
                        document.body.removeChild(input);
                      };
                      input.click();
                    }}
                  >
                    <AddPhotoAlternateIcon sx={{ fontSize: uploadedCount === 0 ? 48 : 32, color: '#94a3b8' }} />
                    {uploadedCount === 0 && <Typography sx={{ color: '#64748b', fontWeight: 500 }}>Cliquez pour ajouter des photos</Typography>}
                  </Box>
                )}
              </Box>
              {fieldErrors.photos && <Typography color="error" variant="caption" sx={{ mt: 1, display: 'block' }}>{fieldErrors.photos}</Typography>}
            </Box>
          </Box>
        );

      case "variants":
        return (
          <Box sx={{ width: '100%', display: 'flex', flexDirection: 'column', gap: 4 }}>
            <Typography variant="h6" sx={{ mb: 1, fontWeight: 600 }}>
              Spécifications de la Récolte
            </Typography>

            {/* Quantity & Unit */}
            <Grid container spacing={2}>
              <Grid item xs={12} sm={8}>
                <TextField
                  fullWidth
                  label="Quantité"
                  type="number"
                  value={form.quantity}
                  onChange={(e) => updateField("quantity", e.target.value)}
                  error={!!fieldErrors.quantity}
                  helperText={fieldErrors.quantity}
                />
              </Grid>
              <Grid item xs={12} sm={4}>
                <CustomSelect
                  label="Unité"
                  options={attributes.quantityUnits}
                  value={form.quantity_unit}
                  onChange={(val) => updateField("quantity_unit", val)}
                  error={!!fieldErrors.quantity_unit}
                  helperText={fieldErrors.quantity_unit}
                />
              </Grid>
            </Grid>

            {/* Region */}
            <Box sx={{ width: '100%' }}>
              <CustomSelect
                label="Région de production"
                options={attributes.regions}
                value={form.region}
                onChange={(val) => updateField("region", val)}
                error={!!fieldErrors.region}
                helperText={fieldErrors.region}
              />
            </Box>

            {/* Season */}
            <Box sx={{ width: '100%' }}>
              <CustomSelect
                label="Saison"
                options={attributes.harvestSeasons}
                value={form.season}
                onChange={(val) => updateField("season", val)}
                error={!!fieldErrors.season}
                helperText={fieldErrors.season}
              />
            </Box>
          </Box>
        );

      case "price":
        return (
          <Box sx={{ width: '100%', display: 'flex', flexDirection: 'column', gap: 4 }}>
            <Typography variant="h6" sx={{ mb: 1, fontWeight: 600 }}>
              Prix & Mode de transaction
            </Typography>

                {/* Price input - Single row alone */}
                <Box sx={{ width: '100%' }}>
                  <TextField
                    fullWidth
                    label="Prix"
                    type="number"
                    size="medium"
                    value={form.price}
                    onChange={(e) => updateField("price", e.target.value)}
                    error={!!fieldErrors.price}
                    helperText={fieldErrors.price}
                    slotProps={{
                      input: {
                        endAdornment: <InputAdornment position="end">MAD</InputAdornment>,
                      }
                    }}
                  />
                </Box>
                
                {/* Negotiable checkbox - Single row alone */}
                <Box sx={{ width: '100%', display: 'flex', alignItems: 'center', gap: 1.5 }}>
                  <input 
                    type="checkbox" 
                    id="negotiable"
                    checked={form.price_negotiable}
                    onChange={(e) => updateField("price_negotiable", e.target.checked)}
                    style={{ width: 22, height: 22, cursor: 'pointer' }}
                  />
                  <label htmlFor="negotiable" style={{ cursor: 'pointer', fontWeight: 600, color: '#1e293b', fontSize: '1rem' }}>
                    Le prix est négociable
                  </label>
                </Box>
          </Box>
        );

      case "location":
        return (
          <Box sx={{ width: '100%', display: 'flex', flexDirection: 'column', gap: 4 }}>
            <Typography variant="h6" sx={{ mb: 1, fontWeight: 600 }}>
              Localisation & Remise
            </Typography>

            {/* Mode de remise cards - Stacked vertically */}
            <Box sx={{ width: '100%' }}>
              <Typography variant="subtitle2" sx={{ mb: 1.5, fontWeight: 600, color: '#475569' }}>Mode de remise</Typography>
              <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                {[
                  { label: "Remise en main propre", value: "pickup" },
                  { label: "Livraison", value: "delivery" },
                  { label: "Les deux", value: "both" }
                ].map((opt) => (
                  <Box
                    key={opt.value}
                    onClick={() => updateField("handover_method", opt.value)}
                    sx={{
                      width: '100%',
                      p: 2.5,
                      borderRadius: 3,
                      border: '2px solid',
                      borderColor: form.handover_method === opt.value ? '#3b82f6' : '#e2e8f0',
                      bgcolor: form.handover_method === opt.value ? '#eff6ff' : '#fff',
                      cursor: 'pointer',
                      transition: 'all 0.2s',
                      display: 'flex',
                      alignItems: 'center',
                      gap: 2,
                      '&:hover': { borderColor: '#3b82f6', bgcolor: '#f8fafc' }
                    }}
                  >
                    <Box sx={{ 
                      width: 24, 
                      height: 24, 
                      borderRadius: '50%', 
                      border: '2px solid',
                      borderColor: form.handover_method === opt.value ? '#3b82f6' : '#cbd5e1',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      bgcolor: '#fff'
                    }}>
                      {form.handover_method === opt.value && (
                        <Box sx={{ width: 12, height: 12, borderRadius: '50%', bgcolor: '#3b82f6' }} />
                      )}
                    </Box>
                    <Typography variant="body1" sx={{ fontWeight: 700, color: form.handover_method === opt.value ? '#1d4ed8' : '#334155' }}>
                      {opt.label}
                    </Typography>
                  </Box>
                ))}
              </Box>
            </Box>

            {/* Ville - Single row alone */}
            <Box sx={{ width: '100%' }}>
              <CustomSelect
                label="Ville"
                options={attributes.cities}
                value={form.city_id}
                onChange={(val) => updateField("city_id", val)}
                placeholder="Choisissez votre ville..."
                error={!!fieldErrors.city_id}
                helperText={fieldErrors.city_id}
              />
            </Box>

            {/* Adresse exacte - Single row alone */}
            <Box sx={{ width: '100%' }}>
              <TextField
                fullWidth
                label="Adresse exacte"
                placeholder="Ex: Rue 123, Quartier..."
                value={form.pickup_address}
                onChange={(e) => updateField("pickup_address", e.target.value)}
                error={!!fieldErrors.pickup_address}
                helperText={fieldErrors.pickup_address}
                slotProps={{
                  input: {
                    startAdornment: <InputAdornment position="start"><MapPin size={18} /></InputAdornment>,
                  }
                }}
              />
            </Box>

            {/* Contact Téléphonique - Single row alone */}
            <Box sx={{ width: '100%' }}>
              <FormControl fullWidth variant="outlined" error={!!fieldErrors.contact_phone}>
                <InputLabel htmlFor="contact-phone">Numéro de téléphone</InputLabel>
                <OutlinedInput
                  id="contact-phone"
                  label="Numéro de téléphone"
                  placeholder="6XXXXXXXX"
                  value={(form.contact_phone || "").replace('+212', '')}
                  onChange={(e) => {
                    let val = e.target.value.replace(/\D/g, '');
                    if (val.startsWith('0')) val = val.substring(1);
                    if (val.length > 9) val = val.substring(0, 9);
                    updateField("contact_phone", `+212${val}`);
                  }}
                  startAdornment={
                    <InputAdornment position="start">
                      <Box sx={{ display: 'flex', alignItems: 'center', gap: 1, pr: 1, borderRight: '1px solid #cbd5e1', mr: 1.5 }}>
                        <span style={{ fontSize: '20px' }}>🇲🇦</span>
                        <Typography variant="body2" sx={{ fontWeight: 700, color: '#1e293b' }}>+212</Typography>
                      </Box>
                    </InputAdornment>
                  }
                />
                {fieldErrors.contact_phone && (
                  <FormHelperText id="contact-phone-error-text">
                    {fieldErrors.contact_phone}
                  </FormHelperText>
                )}
              </FormControl>
            </Box>
          </Box>
        );
      default:
        return null;
    }
  };

  return (
    <Container maxWidth={false} sx={{ py: 0, px: 0 }}>
      <Dialog open={authDialogOpen} onClose={() => {}} fullWidth maxWidth="xs">
        <DialogTitle sx={{ fontWeight: 800 }}>
          Connexion requise
        </DialogTitle>
        <DialogContent>
          <Typography variant="body2" sx={{ color: "#475569", mb: 2 }}>
            Connectez-vous pour publier une annonce et ajouter des photos.
          </Typography>
          <Button fullWidth variant="outlined" sx={{ mb: 1.5 }} onClick={() => {}}>
            Se connecter avec Google
          </Button>
          <Button
            fullWidth
            variant="contained"
            sx={{ bgcolor: "#3b82f6", "&:hover": { bgcolor: "#2563eb" } }}
            onClick={() => {
              window.location.assign("/login");
            }}
          >
            Se connecter avec email
          </Button>
        </DialogContent>
        <DialogActions sx={{ px: 3, pb: 2 }}>
          <Button
            variant="text"
            onClick={() => {
              window.location.assign("/");
            }}
          >
            Retour
          </Button>
        </DialogActions>
      </Dialog>

      <Grid container spacing={0} sx={{ width: '100%', m: 0 }}>
        {/* Left Column - Form (70%) - Sticky left edge, no padding */}
        <Grid item xs={12} md={8.4} sx={{ 
          flexBasis: { md: '70% !important' },
          maxWidth: { md: '70% !important' },
          width: { md: '70% !important' },
          p: 0,
          m: 0
        }}>
          <Paper elevation={0} sx={{ p: { xs: 2, md: 6 }, borderRadius: 0, borderRight: '1px solid #e2e8f0', minHeight: '100vh', width: '100%' }}>
            <Typography variant="h5" align="center" gutterBottom sx={{ fontWeight: 700, mb: 4 }}>
              Publier une annonce
            </Typography>

            <Stepper 
              steps={visibleSteps} 
              currentStep={currentStepNumber} 
              onStepClick={(targetNumber) => {
                const targetKey = visibleSteps[targetNumber - 1]?.key;
                if (targetKey) {
                  setStepKey(targetKey);
                }
              }} 
            />

            <Box sx={{ mt: 4, minHeight: '400px', width: '100%' }}>
              {loading ? (
                <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '400px' }}>
                  <CircularProgress />
                </Box>
              ) : (
                <>
                  {status && (
                    <Box sx={{ 
                      p: 2, 
                      mb: 3, 
                      borderRadius: 2, 
                      bgcolor: status.type === 'success' ? '#f0fdf4' : '#fef2f2',
                      color: status.type === 'success' ? '#166534' : '#991b1b',
                      border: `1px solid ${status.type === 'success' ? '#bbf7d0' : '#fecaca'}`
                    }}>
                      {status.message}
                    </Box>
                  )}
                  {renderStep()}
                </>
              )}
            </Box>

            <Box sx={{ mt: 6, display: 'flex', justifyContent: 'space-between' }}>
              <Button
                variant="outlined"
                onClick={currentStepNumber === 1 ? () => navigate("/user_dashboard") : goPrev}
                sx={{ borderRadius: 2, px: 4, textTransform: 'none', fontWeight: 600 }}
              >
                Retour
              </Button>
              
              {!isLastStep ? (
                <Button
                  variant="contained"
                  color="primary"
                  onClick={goNext}
                  sx={{ borderRadius: 2, px: 4, bgcolor: '#3b82f6', '&:hover': { bgcolor: '#2563eb' }, textTransform: 'none', fontWeight: 600 }}
                >
                  Suivant
                </Button>
              ) : (
                <Button
                  variant="contained"
                  color="primary"
                  onClick={submitAnnouncement}
                  disabled={isUploading}
                  sx={{ borderRadius: 2, px: 4, bgcolor: '#3b82f6', '&:hover': { bgcolor: '#2563eb' }, textTransform: 'none', fontWeight: 600, width: { xs: '100%', sm: 'auto' } }}
                >
                  {isUploading ? (
                    <CircularProgress size={24} color="inherit" />
                  ) : isEditMode ? (
                    "Mettre à jour"
                  ) : (
                    "Publier l'annonce"
                  )}
                </Button>
              )}
            </Box>
          </Paper>
        </Grid>

        {/* Right Column - Preview (30%) - Sticky right edge, no padding */}
        <Grid item xs={12} md={3.6} sx={{ 
          flexBasis: { md: '30% !important' },
          maxWidth: { md: '30% !important' },
          width: { md: '30% !important' },
          p: 0,
          m: 0,
          bgcolor: '#f8fafc' // Subtle background for the preview column
        }}>
          <Box sx={{ position: 'sticky', top: 0, width: '100%', height: '100vh', overflowY: 'auto', p: 4 }}>
            <Paper 
              elevation={0} 
              sx={{ 
                p: 3, 
                borderRadius: 4, 
                border: '1px solid #e2e8f0', 
                bgcolor: '#fff',
                boxShadow: '0 4px 20px rgba(0,0,0,0.05)',
                width: '100%'
              }}
            >
              <Typography variant="h6" sx={{ mb: 3, fontWeight: 700, color: '#1e293b', display: 'flex', alignItems: 'center', gap: 1 }}>
                <Box sx={{ width: 4, height: 18, bgcolor: '#3b82f6', borderRadius: 1 }} />
                Aperçu de l'annonce
              </Typography>

              {/* Main Photo Preview */}
              <Box sx={{ 
                width: '100%', 
                aspectRatio: '1/1', 
                borderRadius: 3, 
                bgcolor: '#f1f5f9',
                overflow: 'hidden',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                mb: 3,
                position: 'relative',
                border: '1px solid #e2e8f0'
              }}>
                {uploadSlots.find(s => s.status === 'done')?.url ? (
                  <img 
                    src={uploadSlots.find(s => s.status === 'done')!.url!}
                    alt="Principale" 
                    style={{ width: '100%', height: '100%', objectFit: 'cover' }} 
                  />
                ) : (
                  <Box sx={{ textAlign: 'center', color: '#94a3b8' }}>
                    <AddPhotoAlternateIcon sx={{ fontSize: 48, mb: 1, opacity: 0.5 }} />
                    <Typography variant="body2" sx={{ fontWeight: 500 }}>Aucune photo</Typography>
                  </Box>
                )}
                <Box sx={{ 
                  position: 'absolute', 
                  top: 12, 
                  left: 12, 
                  bgcolor: '#3b82f6', 
                  color: '#fff', 
                  px: 1.5, 
                  py: 0.5, 
                  borderRadius: 1.5, 
                  fontSize: '0.75rem',
                  fontWeight: 700,
                  boxShadow: '0 2px 8px rgba(0,0,0,0.15)',
                  textTransform: 'uppercase'
                }}>
                  {`${form.price || 0} MAD`}
                </Box>
              </Box>

              <Typography variant="h6" sx={{ fontWeight: 700, mb: 1, color: '#1e293b', lineHeight: 1.3 }}>
                {form.title || "Titre de l'annonce"}
              </Typography>
              
              <Typography variant="body2" sx={{ color: '#64748b', mb: 3, minHeight: '3em', display: '-webkit-box', WebkitLineClamp: 3, WebkitBoxOrient: 'vertical', overflow: 'hidden' }}>
                {form.description || "Votre description apparaîtra ici..."}
              </Typography>

              <Divider sx={{ mb: 3, borderStyle: 'dashed' }} />

              <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2.5 }}>
                {/* Category & Sub-categories */}
                <Box>
                  <Typography variant="caption" sx={{ color: '#94a3b8', fontWeight: 600, textTransform: 'uppercase', fontSize: '0.65rem', letterSpacing: '0.05em', display: 'block', mb: 0.5 }}>Catégorie</Typography>
                  <Typography variant="body2" sx={{ fontWeight: 700, color: '#1e293b' }}>{form.super_category_name || "-"}</Typography>
                  {form.sub_category_names.length > 0 && (
                    <Box component="ul" sx={{ m: 0, mt: 0.5, pl: 2, color: '#64748b' }}>
                      {form.sub_category_names.map((name, i) => (
                        <Box component="li" key={i} sx={{ fontSize: '0.75rem', fontWeight: 500, mb: 0.2 }}>
                          {name}
                        </Box>
                      ))}
                    </Box>
                  )}
                </Box>

                {/* Brand */}
                {form.brand && (
                  <Box>
                    <Typography variant="caption" sx={{ color: '#94a3b8', fontWeight: 600, textTransform: 'uppercase', fontSize: '0.65rem', letterSpacing: '0.05em', display: 'block', mb: 0.5 }}>Marque</Typography>
                    <Typography variant="body2" sx={{ fontWeight: 600, color: '#334155' }}>{form.brand}</Typography>
                  </Box>
                )}

                {/* Condition */}
                <Box>
                  <Typography variant="caption" sx={{ color: '#94a3b8', fontWeight: 600, textTransform: 'uppercase', fontSize: '0.65rem', letterSpacing: '0.05em', display: 'block', mb: 0.5 }}>État</Typography>
                  <Typography variant="body2" sx={{ fontWeight: 600, color: '#334155' }}>
                    {form.condition === 'new_tag' ? 'Neuf avec étiquette' : 
                     form.condition === 'new_no_tag' ? 'Neuf sans étiquette' :
                     form.condition === 'very_good' ? 'Très bon état' : 
                     form.condition === 'good' ? 'Bon état' : 
                     form.condition === 'fair' ? 'Satisfaisant' : '-'}
                  </Typography>
                </Box>

                {/* Sizes */}
                {form.sizes.length > 0 && (
                  <Box>
                    <Typography variant="caption" sx={{ color: '#94a3b8', fontWeight: 600, textTransform: 'uppercase', fontSize: '0.65rem', letterSpacing: '0.05em', display: 'block', mb: 0.5 }}>Tailles</Typography>
                    <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
                      {form.sizes.map(size => (
                        <Chip key={size} label={size} size="small" sx={{ height: 20, fontSize: '0.7rem', fontWeight: 600, bgcolor: '#f1f5f9' }} />
                      ))}
                    </Box>
                  </Box>
                )}

                {/* Colors */}
                {form.colors.length > 0 && (
                  <Box>
                    <Typography variant="caption" sx={{ color: '#94a3b8', fontWeight: 600, textTransform: 'uppercase', fontSize: '0.65rem', letterSpacing: '0.05em', display: 'block', mb: 0.5 }}>Couleurs</Typography>
                    <Typography variant="body2" sx={{ fontWeight: 600, color: '#334155' }}>{form.colors.join(', ')}</Typography>
                  </Box>
                )}

                {/* Season */}
                {form.season && (
                  <Box>
                    <Typography variant="caption" sx={{ color: '#94a3b8', fontWeight: 600, textTransform: 'uppercase', fontSize: '0.65rem', letterSpacing: '0.05em', display: 'block', mb: 0.5 }}>Saison</Typography>
                    <Typography variant="body2" sx={{ fontWeight: 600, color: '#334155' }}>{form.season}</Typography>
                  </Box>
                )}

                {/* Material */}
                {form.material && (
                  <Box>
                    <Typography variant="caption" sx={{ color: '#94a3b8', fontWeight: 600, textTransform: 'uppercase', fontSize: '0.65rem', letterSpacing: '0.05em', display: 'block', mb: 0.5 }}>Matière</Typography>
                    <Typography variant="body2" sx={{ fontWeight: 600, color: '#334155' }}>{form.material}</Typography>
                  </Box>
                )}

                {/* Handover Method */}
                <Box>
                  <Typography variant="caption" sx={{ color: '#94a3b8', fontWeight: 600, textTransform: 'uppercase', fontSize: '0.65rem', letterSpacing: '0.05em', display: 'block', mb: 0.5 }}>Mode de remise</Typography>
                  <Typography variant="body2" sx={{ fontWeight: 600, color: '#334155' }}>
                    {form.handover_method === 'pickup' ? 'Remise en main propre' : 
                     form.handover_method === 'delivery' ? 'Livraison' : 
                     form.handover_method === 'both' ? 'Main propre & Livraison' : '-'}
                  </Typography>
                </Box>
              </Box>

              <Box sx={{ mt: 4, p: 2, bgcolor: '#f1f5f9', borderRadius: 2.5, border: '1px solid #e2e8f0', display: 'flex', alignItems: 'center', gap: 1.5 }}>
                <MapPin size={18} color="#64748b" />
                <Typography variant="body2" sx={{ color: '#475569', fontWeight: 500 }}>
                  {form.city_id ? `${attributes.cities.find(c => String(c.id || c.value) === String(form.city_id))?.label || ""}, ` : ""}{form.pickup_address || "Localisation..."}
                </Typography>
              </Box>
            </Paper>

            <Box sx={{ mt: 2, p: 2, bgcolor: '#eff6ff', borderRadius: 3, border: '1px solid #dbeafe', display: 'flex', gap: 2, alignItems: 'center' }}>
                <Box sx={{ width: 40, height: 40, borderRadius: '50%', bgcolor: '#3b82f6', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff' }}>
                  <Typography variant="subtitle2" sx={{ fontWeight: 800, width: '100%', textAlign: 'center' }}>?</Typography>
                </Box>
              <Typography variant="caption" sx={{ color: '#1e40af', fontWeight: 500, lineHeight: 1.4 }}>
                Besoin d'aide ? Consultez nos conseils pour une annonce réussie.
              </Typography>
            </Box>
          </Box>
        </Grid>
      </Grid>

      {/* Success Toast */}
      <Snackbar
        open={toastOpen}
        autoHideDuration={4000}
        onClose={() => setToastOpen(false)}
        anchorOrigin={{ vertical: 'bottom', horizontal: 'center' }}
      >
        <Alert 
          onClose={() => setToastOpen(false)} 
          severity="success" 
          sx={{ width: '100%', borderRadius: 3, fontWeight: 600, boxShadow: '0 8px 32px rgba(0,0,0,0.1)' }}
        >
          {isEditMode ? "Annonce mise à jour avec succès !" : "Félicitations ! Votre annonce a été publiée avec succès."}
        </Alert>
      </Snackbar>
    </Container>
  );
}
