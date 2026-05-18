import { 
  Search, 
  ChevronRight, 
  Check,
  Camera,
  X,
  ChevronDown,
  Leaf,
  Calendar,
  Map as MapIcon,
  Scale
} from 'lucide-react';
import { 
  MapPoint as MapPin, 
  Bag as ShoppingBag, 
  Gift,
  Shop as Store,
  Tag,
  User,
  UsersGroupRounded as People
} from '@solar-icons/react';
import { useTheme } from '../../../context/ThemeContext';
import CustomSelect from '../common/CustomSelect';

interface SidebarProps {
  initData: any;
  filters: any;
  onFilterChange: (key: string, value: any) => void;
  onToggleArrayFilter: (key: string, value: any) => void;
  onReset: () => void;
  onApply: () => void;
  resultsCount: number;
  loading?: boolean;
}

const Sidebar: React.FC<SidebarProps> = ({
  initData,
  filters,
  onFilterChange,
  onToggleArrayFilter,
  onReset,
  onApply,
  resultsCount,
  loading = false
}) => {
  const { colors } = useTheme();

  const SectionLabel = ({ children }: { children: React.ReactNode }) => (
    <div style={{
      fontSize: '12px',
      fontWeight: '600',
      color: colors.textSecondary,
      textTransform: 'uppercase',
      letterSpacing: '0.5px',
      marginBottom: '8px'
    }}>
      {children}
    </div>
  );

  const Skeleton = () => (
    <div style={{ height: '40px', backgroundColor: colors.bgTertiary, borderRadius: '10px', marginBottom: '20px', animation: 'pulse 1.5s infinite' }} />
  );

  if (loading && !initData) {
    return (
      <aside style={{ 
        width: '280px', 
        borderRight: `1px solid ${colors.sidebarBorder}`, 
        backgroundColor: colors.bgSecondary, 
        height: 'calc(100vh - 80px)', 
        position: 'sticky',
        top: '80px',
        padding: '16px' 
      }}>
        {[1,2,3,4,5,6,7,8].map(i => <Skeleton key={i} />)}
      </aside>
    );
  }

  return (
    <aside style={{ 
      width: '280px', 
      borderRight: `1px solid ${colors.sidebarBorder}`, 
      backgroundColor: colors.bgSecondary, 
      height: 'calc(100vh - 80px)', 
      position: 'sticky', 
      top: '80px', 
      display: 'flex', 
      flexDirection: 'column',
      zIndex: 10
    }}>
      {/* Top Row — Clear Only */}
      <div style={{ 
        padding: '10px 16px', 
        backgroundColor: colors.filterBg, 
        borderBottom: `1px solid ${colors.filterBorder}`,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'flex-end',
        flexShrink: 0
      }}>
        <button onClick={onReset} style={{ background: 'none', border: 'none', color: colors.textSecondary, fontSize: '12px', cursor: 'pointer', padding: 0 }}>Effacer tout</button>
      </div>

      {/* Scrollable Content */}
      <div className="custom-scrollbar" style={{ 
        padding: '12px 16px', 
        overflowY: 'auto', 
        flex: 1,
        scrollbarWidth: 'thin',
        scrollbarColor: `${colors.coral} ${colors.scrollbarTrack}`
      }}>
        {/* CSS for scrollbar */}
        <style>{`
          .custom-scrollbar::-webkit-scrollbar { width: 3px; }
          .custom-scrollbar::-webkit-scrollbar-track { background: ${colors.scrollbarTrack}; }
          .custom-scrollbar::-webkit-scrollbar-thumb { background: ${colors.coral}; border-radius: 10px; }
        `}</style>

        {/* Catégorie */}
        <div style={{ marginBottom: '20px' }}>
          <SectionLabel>Catégorie</SectionLabel>
          <CustomSelect 
            options={initData?.categories || []}
            value={filters.category}
            onChange={(val) => onFilterChange('category', val)}
            placeholder="Choisir catégorie"
            icon={<Leaf size={18} weight="bold" color={colors.iconCoral} />}
          />
        </div>

        {/* Région */}
        <div style={{ marginBottom: '20px' }}>
          <SectionLabel>Région</SectionLabel>
          <CustomSelect 
            multiple={true}
            searchable={true}
            options={initData?.regions || []}
            value={filters.regions || []}
            onChange={(val) => onFilterChange('regions', val)}
            placeholder="Toutes les régions"
            icon={<MapIcon size={18} weight="bold" color={colors.iconCoral} />}
          />
          {/* Selected region pills */}
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: '6px', marginTop: '8px' }}>
            {(filters.regions || []).map((regionId: any) => {
              const region = initData?.regions?.find((r: any) => r.id === regionId);
              if (!region) return null;
              return (
                <div key={regionId} style={{ display: 'flex', alignItems: 'center', gap: '4px', padding: '4px 8px', borderRadius: '6px', border: `1px solid ${colors.infoText}`, backgroundColor: colors.infoBg, color: colors.infoText, fontSize: '11px', fontWeight: '600' }}>
                  {region.label}
                  <X size={12} style={{ cursor: 'pointer' }} onClick={() => onToggleArrayFilter('regions', regionId)} strokeWidth={2} />
                </div>
              );
            })}
          </div>
        </div>

        {/* Ville - Secteur */}
        <div style={{ marginBottom: '20px' }}>
          <SectionLabel>Ville - Secteur</SectionLabel>
          <CustomSelect 
            multiple={true}
            searchable={true}
            options={initData?.cities || []}
            value={filters.cities || []}
            onChange={(val) => onFilterChange('cities', val)}
            placeholder="Toutes les villes"
            icon={<MapPin size={18} weight="BoldDuotone" color={colors.iconCoral} />}
          />
        </div>


        {/* Saison de récolte */}
        <div style={{ marginBottom: '20px' }}>
          <SectionLabel>Saison de récolte</SectionLabel>
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px' }}>
            {initData?.harvestSeasons?.map((season: any) => {
              const active = filters.harvest_season?.includes(season.value);
              return (
                <button 
                  key={season.value}
                  onClick={() => onToggleArrayFilter('harvest_season', season.value)}
                  style={{ 
                    padding: '6px 12px', 
                    borderRadius: '20px', 
                    border: active ? 'none' : `1px solid ${colors.border}`,
                    backgroundColor: active ? colors.coral : colors.bgSecondary,
                    color: active ? colors.bgSecondary : colors.textSecondary,
                    fontSize: '12px',
                    fontWeight: '600',
                    cursor: 'pointer'
                  }}
                >
                  {season.label}
                </button>
              );
            })}
          </div>
        </div>

        {/* Unité de quantité */}
        <div style={{ marginBottom: '20px' }}>
          <SectionLabel>Unité de quantité</SectionLabel>
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: '8px' }}>
            {initData?.quantityUnits?.map((unit: any) => {
              const active = filters.quantity_unit === unit.value;
              return (
                <button 
                  key={unit.value}
                  onClick={() => onFilterChange('quantity_unit', filters.quantity_unit === unit.value ? "" : unit.value)}
                  style={{ 
                    padding: '6px 12px', 
                    borderRadius: '20px', 
                    border: active ? 'none' : `1px solid ${colors.border}`,
                    backgroundColor: active ? colors.coral : colors.bgSecondary,
                    color: active ? colors.bgSecondary : colors.textSecondary,
                    fontSize: '12px',
                    fontWeight: '600',
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '4px'
                  }}
                >
                  <Scale size={14} />
                  {unit.label}
                </button>
              );
            })}
          </div>
        </div>

        {/* État */}
        <div style={{ marginBottom: '20px' }}>
          <SectionLabel>État du produit</SectionLabel>
          <div style={{ display: 'flex', flexDirection: 'column', gap: '4px' }}>
            {initData?.conditions?.map((cond: any) => {
              const active = filters.condition === cond.value;
              return (
                <div 
                  key={cond.value}
                  onClick={() => onFilterChange('condition', filters.condition === cond.value ? "" : cond.value)}
                  style={{ 
                    display: 'flex', 
                    alignItems: 'center', 
                    gap: '10px', 
                    padding: '10px 12px', 
                    cursor: 'pointer',
                    borderRadius: '0 8px 8px 0',
                    borderLeft: active ? `3px solid ${colors.coral}` : '3px solid transparent',
                    backgroundColor: active ? colors.coralLight : 'transparent',
                    transition: 'all 0.2s'
                  }}
                >
                  <div style={{ width: '8px', height: '8px', borderRadius: '50%', backgroundColor: cond.color }} />
                  <span style={{ fontSize: '13px', color: active ? colors.coral : colors.textPrimary, fontWeight: active ? '600' : '400' }}>{cond.label}</span>
                </div>
              );
            })}
          </div>
        </div>

        {/* Prix */}
        <div style={{ marginBottom: '20px' }}>
          <SectionLabel>Prix</SectionLabel>
          <div style={{ display: 'flex', gap: '10px', alignItems: 'center', marginBottom: '12px' }}>
            <div style={{ position: 'relative', flex: 1 }}>
              <input 
                type="number" 
                placeholder="Min" 
                value={filters.min_price}
                onChange={(e) => onFilterChange('min_price', e.target.value)}
                style={{ width: '100%', padding: '8px 40px 8px 12px', borderRadius: '8px', border: `1px solid ${colors.border}`, fontSize: '13px', backgroundColor: colors.bgSecondary, color: colors.textPrimary }}
              />
              <span style={{ position: 'absolute', right: '10px', top: '50%', transform: 'translateY(-50%)', fontSize: '11px', color: colors.textMuted }}>MAD</span>
            </div>
            <div style={{ position: 'relative', flex: 1 }}>
              <input 
                type="number" 
                placeholder="Max" 
                value={filters.max_price}
                onChange={(e) => onFilterChange('max_price', e.target.value)}
                style={{ width: '100%', padding: '8px 40px 8px 12px', borderRadius: '8px', border: `1px solid ${colors.border}`, fontSize: '13px', backgroundColor: colors.bgSecondary, color: colors.textPrimary }}
              />
              <span style={{ position: 'absolute', right: '10px', top: '50%', transform: 'translateY(-50%)', fontSize: '11px', color: colors.textMuted }}>MAD</span>
            </div>
          </div>
        </div>

        {/* Toggle Switches (bottom) */}
        <div style={{ marginBottom: '20px', display: 'flex', flexDirection: 'column', gap: '12px' }}>
          {[
            { key: 'with_media', label: 'Annonces avec photos uniquement', icon: <Camera size={14} strokeWidth={2} /> }
          ].map((item) => (
            <div key={item.key} style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
              <div style={{ backgroundColor: colors.darkNavy, color: colors.bgSecondary, padding: '6px', borderRadius: '8px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                {item.icon}
              </div>
              <span style={{ flex: 1, fontSize: '12px', color: colors.textPrimary }}>{item.label}</span>
              <div 
                onClick={() => onFilterChange(item.key, !filters[item.key])}
                style={{ width: '36px', height: '20px', borderRadius: '10px', backgroundColor: filters[item.key] ? colors.coral : colors.bgTertiary, position: 'relative', cursor: 'pointer' }}
              >
                <div style={{ width: '16px', height: '16px', borderRadius: '50%', backgroundColor: colors.bgSecondary, position: 'absolute', left: filters[item.key] ? '18px' : '2px', top: '2px', transition: 'left 0.2s' }} />
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Apply Button */}
      <div style={{ 
        padding: '16px', 
        borderTop: `1px solid ${colors.border}`, 
        backgroundColor: colors.bgSecondary,
        position: 'sticky',
        bottom: 0,
        zIndex: 5
      }}>
        <button 
          onClick={onApply}
          style={{ 
            width: '100%', 
            padding: '14px', 
            backgroundColor: colors.coral, 
            color: colors.bgSecondary, 
            border: 'none', 
            borderRadius: '12px', 
            fontWeight: '700', 
            fontSize: '14px',
            cursor: 'pointer',
            boxShadow: `0 4px 12px ${colors.coral}33`,
            transition: 'all 0.2s'
          }}
          onMouseOver={(e) => {
            e.currentTarget.style.backgroundColor = colors.coralHover;
            e.currentTarget.style.transform = 'translateY(-2px)';
          }}
          onMouseOut={(e) => {
            e.currentTarget.style.backgroundColor = colors.coral;
            e.currentTarget.style.transform = 'translateY(0)';
          }}
        >
          {loading ? 'Chargement...' : `Voir les annonces (${resultsCount})`}
        </button>
      </div>
    </aside>
  );
};

export default Sidebar;
