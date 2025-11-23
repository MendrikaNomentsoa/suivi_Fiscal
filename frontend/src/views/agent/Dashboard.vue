<template>
  <div class="dashboard-container">
    <!-- Header avec informations agent -->
    <header class="dashboard-header">
      <div class="header-content">
        <div class="logo">
          <h1>🏢 Espace Agent Fiscal</h1>
        </div>
        <div class="user-info">
          <div class="user-details">
            <span class="user-name">{{ user?.prenom }} {{ user?.nom }}</span>
            <span class="user-role">{{ getRoleLabel(user?.role) }}</span>
          </div>
          <button @click="handleLogout" class="btn-logout">
            🚪 Déconnexion
          </button>
        </div>
      </div>
    </header>

    <!-- Navigation rapide -->
    <nav class="quick-nav">
      <button 
        v-for="tab in tabs" 
        :key="tab.id"
        :class="['nav-btn', { active: activeTab === tab.id }]"
        @click="activeTab = tab.id"
      >
        <span class="nav-icon">{{ tab.icon }}</span>
        <span>{{ tab.label }}</span>
      </button>
    </nav>

    <!-- Contenu principal -->
    <main class="dashboard-main">
      
      <!-- Vue d'ensemble -->
      <div v-if="activeTab === 'overview'">
        <!-- Section de bienvenue -->
        <section class="welcome-section">
          <h2>Tableau de bord</h2>
          <p>Vue d'ensemble de l'activité fiscale</p>
        </section>

        <!-- Statistiques globales -->
        <section class="stats-section">
          <div class="stat-card stat-primary">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
              <h3>{{ stats.totalContribuables }}</h3>
              <p>Contribuables enregistrés</p>
            </div>
          </div>

          <div class="stat-card stat-success">
            <div class="stat-icon">📝</div>
            <div class="stat-content">
              <h3>{{ stats.declarationsValidees }}</h3>
              <p>Déclarations validées</p>
            </div>
          </div>

          <div class="stat-card stat-warning">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
              <h3>{{ stats.declarationsEnAttente }}</h3>
              <p>En attente de validation</p>
            </div>
          </div>

          <div class="stat-card stat-danger">
            <div class="stat-icon">⚠️</div>
            <div class="stat-content">
              <h3>{{ stats.litigesOuverts }}</h3>
              <p>Litiges en cours</p>
            </div>
          </div>
        </section>

        <!-- Actions rapides -->
        <section class="actions-section">
          <h3>Actions rapides</h3>
          <div class="action-cards">
            <div class="action-card" @click="activeTab = 'litiges'">
              <div class="action-icon">⚖️</div>
              <h4>Gérer les litiges</h4>
              <p>Traiter les litiges en attente</p>
              <span class="badge" v-if="stats.litigesEnAttente > 0">
                {{ stats.litigesEnAttente }}
              </span>
            </div>

            <div class="action-card" @click="activeTab = 'declarations'">
              <div class="action-icon">📋</div>
              <h4>Vérifier les déclarations</h4>
              <p>Consulter toutes les déclarations</p>
            </div>

            <div class="action-card" @click="activeTab = 'contribuables'">
              <div class="action-icon">👤</div>
              <h4>Gérer les contribuables</h4>
              <p>Consulter la liste des contribuables</p>
            </div>

            <div class="action-card" @click="activeTab = 'statistiques'">
              <div class="action-icon">📊</div>
              <h4>Voir les statistiques</h4>
              <p>Analyses et rapports détaillés</p>
            </div>
          </div>
        </section>

        <!-- Activité récente -->
        <section class="activity-section">
          <h3>Activité récente</h3>
          <div class="activity-list">
            <div 
              v-for="activity in recentActivities" 
              :key="activity.id"
              class="activity-item"
            >
              <div class="activity-icon" :class="activity.type">
                {{ activity.icon }}
              </div>
              <div class="activity-content">
                <p class="activity-text">{{ activity.text }}</p>
                <span class="activity-time">{{ activity.time }}</span>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Gestion des litiges -->
      <div v-else-if="activeTab === 'litiges'">
        <section class="content-section">
          <div class="section-header">
            <h2>Gestion des litiges</h2>
            <div class="filter-buttons">
              <button 
                :class="['filter-btn', { active: litigeFilter === 'all' }]"
                @click="litigeFilter = 'all'"
              >
                Tous ({{ litiges.length }})
              </button>
              <button 
                :class="['filter-btn', { active: litigeFilter === 'en_attente' }]"
                @click="litigeFilter = 'en_attente'"
              >
                En attente ({{ litigesEnAttente.length }})
              </button>
              <button 
                :class="['filter-btn', { active: litigeFilter === 'en_cours' }]"
                @click="litigeFilter = 'en_cours'"
              >
                En cours ({{ litigesEnCours.length }})
              </button>
              <button 
                :class="['filter-btn', { active: litigeFilter === 'resolu' }]"
                @click="litigeFilter = 'resolu'"
              >
                Résolus ({{ litigesResolus.length }})
              </button>
            </div>
          </div>

          <div v-if="litigesFiltres.length > 0" class="litiges-grid">
            <div 
              v-for="litige in litigesFiltres" 
              :key="litige.id_Litige"
              class="litige-card"
            >
              <div class="litige-header">
                <h4>{{ litige.sujet }}</h4>
                <span :class="['litige-statut', litige.statut]">
                  {{ getStatutLabel(litige.statut) }}
                </span>
              </div>
              <p class="litige-description">{{ litige.description }}</p>
              <div class="litige-footer">
                <span class="litige-contribuable">
                  👤 {{ litige.contribuable?.nom }} {{ litige.contribuable?.prenom }}
                </span>
                <span class="litige-date">
                  📅 {{ formatDate(litige.date_ouverture) }}
                </span>
              </div>
              <div class="litige-actions">
                <button 
                  v-if="litige.statut === 'en_attente'"
                  @click="assignerLitige(litige.id_Litige)"
                  class="btn-primary-small"
                >
                  Prendre en charge
                </button>
                <button 
                  @click="voirDetailsLitige(litige)"
                  class="btn-secondary-small"
                >
                  Détails
                </button>
              </div>
            </div>
          </div>

          <div v-else class="empty-state">
            <p>Aucun litige dans cette catégorie</p>
          </div>
        </section>
      </div>

      <!-- Déclarations -->
      <div v-else-if="activeTab === 'declarations'">
        <section class="content-section">
          <div class="section-header">
            <h2>Déclarations fiscales</h2>
            <input 
              type="search" 
              v-model="searchDeclaration"
              placeholder="Rechercher par NIF ou nom..."
              class="search-input"
            />
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Contribuable</th>
                  <th>Type d'impôt</th>
                  <th>Montant</th>
                  <th>Date</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="decl in declarationsFiltrees" :key="decl.id_declaration">
                  <td>
                    <div class="contrib-info">
                      <strong>{{ decl.contribuable?.nom }} {{ decl.contribuable?.prenom }}</strong>
                      <small>NIF: {{ decl.contribuable?.nif }}</small>
                    </div>
                  </td>
                  <td>{{ decl.type_impot?.nom }}</td>
                  <td><strong>{{ formatMontant(decl.montant) }}</strong></td>
                  <td>{{ formatDate(decl.date_declaration) }}</td>
                  <td>
                    <span :class="['table-statut', decl.statut]">
                      {{ getStatutLabel(decl.statut) }}
                    </span>
                  </td>
                  <td>
                    <button @click="voirDetailsDeclaration(decl)" class="btn-icon">
                      👁️
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <!-- Contribuables -->
      <div v-else-if="activeTab === 'contribuables'">
        <section class="content-section">
          <div class="section-header">
            <h2>Liste des contribuables</h2>
            <input 
              type="search" 
              v-model="searchContribuable"
              placeholder="Rechercher par NIF, nom ou email..."
              class="search-input"
            />
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>NIF</th>
                  <th>Nom complet</th>
                  <th>Email</th>
                  <th>Téléphone</th>
                  <th>Inscription</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="contrib in contribuablesFiltres" :key="contrib.id_contribuable">
                  <td><strong>{{ contrib.nif }}</strong></td>
                  <td>{{ contrib.nom }} {{ contrib.prenom }}</td>
                  <td>{{ contrib.email || 'N/A' }}</td>
                  <td>{{ contrib.telephone || 'N/A' }}</td>
                  <td>{{ formatDate(contrib.date_inscription) }}</td>
                  <td>
                    <button @click="voirProfilContribuable(contrib)" class="btn-icon">
                      👁️
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>

      <!-- Statistiques -->
      <div v-else-if="activeTab === 'statistiques'">
        <section class="content-section">
          <h2>Statistiques détaillées</h2>
          
          <div class="stats-grid">
            <div class="stat-box">
              <h4>Déclarations par type</h4>
              <div class="stat-items">
                <div class="stat-row">
                  <span>IRSA:</span>
                  <strong>{{ statsDetails.irsaCount }}</strong>
                </div>
                <div class="stat-row">
                  <span>IS:</span>
                  <strong>{{ statsDetails.isCount }}</strong>
                </div>
              </div>
            </div>

            <div class="stat-box">
              <h4>Revenus fiscaux</h4>
              <div class="stat-items">
                <div class="stat-row">
                  <span>Total perçu:</span>
                  <strong>{{ formatMontant(statsDetails.totalRevenu) }}</strong>
                </div>
                <div class="stat-row">
                  <span>En attente:</span>
                  <strong>{{ formatMontant(statsDetails.revenusEnAttente) }}</strong>
                </div>
              </div>
            </div>

            <div class="stat-box">
              <h4>Taux de conformité</h4>
              <div class="stat-items">
                <div class="stat-row">
                  <span>Dans les délais:</span>
                  <strong>{{ statsDetails.tauxConformite }}%</strong>
                </div>
                <div class="stat-row">
                  <span>En retard:</span>
                  <strong>{{ 100 - statsDetails.tauxConformite }}%</strong>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

    </main>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import axios from 'axios'

export default {
  name: 'AgentDashboard',
  setup() {
    const router = useRouter()
    const { user, logout } = useAuth()

    const activeTab = ref('overview')
    const litigeFilter = ref('all')
    const searchDeclaration = ref('')
    const searchContribuable = ref('')

    const tabs = [
      { id: 'overview', label: 'Vue d\'ensemble', icon: '📊' },
      { id: 'litiges', label: 'Litiges', icon: '⚖️' },
      { id: 'declarations', label: 'Déclarations', icon: '📋' },
      { id: 'contribuables', label: 'Contribuables', icon: '👥' },
      { id: 'statistiques', label: 'Statistiques', icon: '📈' }
    ]

    // Données
    const litiges = ref([])
    const declarations = ref([])
    const contribuables = ref([])
    const stats = ref({
      totalContribuables: 0,
      declarationsValidees: 0,
      declarationsEnAttente: 0,
      litigesOuverts: 0,
      litigesEnAttente: 0
    })

    const statsDetails = ref({
      irsaCount: 0,
      isCount: 0,
      totalRevenu: 0,
      revenusEnAttente: 0,
      tauxConformite: 0
    })

    const recentActivities = ref([])

    // Computed
    const litigesEnAttente = computed(() => 
      litiges.value.filter(l => l.statut === 'en_attente')
    )

    const litigesEnCours = computed(() => 
      litiges.value.filter(l => l.statut === 'en_cours')
    )

    const litigesResolus = computed(() => 
      litiges.value.filter(l => l.statut === 'resolu')
    )

    const litigesFiltres = computed(() => {
      if (litigeFilter.value === 'all') return litiges.value
      return litiges.value.filter(l => l.statut === litigeFilter.value)
    })

    const declarationsFiltrees = computed(() => {
      if (!searchDeclaration.value) return declarations.value
      const search = searchDeclaration.value.toLowerCase()
      return declarations.value.filter(d => 
        d.contribuable?.nif?.toLowerCase().includes(search) ||
        d.contribuable?.nom?.toLowerCase().includes(search) ||
        d.contribuable?.prenom?.toLowerCase().includes(search)
      )
    })

    const contribuablesFiltres = computed(() => {
      if (!searchContribuable.value) return contribuables.value
      const search = searchContribuable.value.toLowerCase()
      return contribuables.value.filter(c => 
        c.nif?.toLowerCase().includes(search) ||
        c.nom?.toLowerCase().includes(search) ||
        c.prenom?.toLowerCase().includes(search) ||
        c.email?.toLowerCase().includes(search)
      )
    })

    // Méthodes
    const loadData = async () => {
      try {
        // Charger les litiges
        const litigesRes = await axios.get('/api/litiges')
        litiges.value = litigesRes.data

        // Charger les déclarations
        const declRes = await axios.get('/api/declarations')
        declarations.value = declRes.data

        // Charger les contribuables
        const contribRes = await axios.get('/api/contribuables')
        contribuables.value = contribRes.data

        // Calculer les stats
        stats.value.totalContribuables = contribuables.value.length
        stats.value.declarationsValidees = declarations.value.filter(d => d.statut === 'validee').length
        stats.value.declarationsEnAttente = declarations.value.filter(d => d.statut === 'brouillon').length
        stats.value.litigesOuverts = litiges.value.filter(l => l.statut !== 'resolu').length
        stats.value.litigesEnAttente = litigesEnAttente.value.length

        // Stats détaillées
        statsDetails.value.irsaCount = declarations.value.filter(d => d.id_type_impot === 1).length
        statsDetails.value.isCount = declarations.value.filter(d => d.id_type_impot === 2).length
        statsDetails.value.totalRevenu = declarations.value
          .filter(d => d.statut === 'validee')
          .reduce((sum, d) => sum + d.montant, 0)
        statsDetails.value.revenusEnAttente = declarations.value
          .filter(d => d.statut === 'brouillon')
          .reduce((sum, d) => sum + d.montant, 0)
        
        const total = declarations.value.length
        const conformes = declarations.value.filter(d => d.statut === 'validee').length
        statsDetails.value.tauxConformite = total > 0 ? Math.round((conformes / total) * 100) : 0

        // Activités récentes
        recentActivities.value = [
          { id: 1, type: 'declaration', icon: '📝', text: 'Nouvelle déclaration IRSA validée', time: 'Il y a 2h' },
          { id: 2, type: 'litige', icon: '⚖️', text: 'Litige assigné à l\'agent Rakoto', time: 'Il y a 4h' },
          { id: 3, type: 'contribuable', icon: '👤', text: 'Nouveau contribuable enregistré', time: 'Il y a 6h' },
          { id: 4, type: 'declaration', icon: '📝', text: 'Déclaration IS en attente de validation', time: 'Il y a 1j' }
        ]

      } catch (error) {
        console.error('Erreur chargement données:', error)
      }
    }

    const assignerLitige = async (idLitige) => {
      try {
        await axios.post('/api/traiter/assigner', {
          id_Litige: idLitige,
          id_Agent: user.value.id_Agent
        })
        alert('Litige assigné avec succès')
        loadData()
      } catch (error) {
        console.error('Erreur assignation:', error)
        alert('Erreur lors de l\'assignation du litige')
      }
    }

    const voirDetailsLitige = (litige) => {
      alert(`Détails du litige:\n\nSujet: ${litige.sujet}\nDescription: ${litige.description}`)
    }

    const voirDetailsDeclaration = (declaration) => {
      alert(`Détails de la déclaration:\n\nType: ${declaration.type_impot?.nom}\nMontant: ${formatMontant(declaration.montant)}`)
    }

    const voirProfilContribuable = (contribuable) => {
      alert(`Profil de ${contribuable.nom} ${contribuable.prenom}\n\nNIF: ${contribuable.nif}\nEmail: ${contribuable.email}`)
    }

    const handleLogout = () => {
      if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        logout()
      }
    }

    // Formatage
    const formatMontant = (montant) => {
      return new Intl.NumberFormat('fr-FR', {
        style: 'decimal',
        minimumFractionDigits: 0
      }).format(montant) + ' Ar'
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      })
    }

    const getStatutLabel = (statut) => {
      const labels = {
        'brouillon': 'Brouillon',
        'validee': 'Validée',
        'en_attente': 'En attente',
        'en_cours': 'En cours',
        'resolu': 'Résolu'
      }
      return labels[statut] || statut
    }

    const getRoleLabel = (role) => {
      const roles = {
        'admin': 'Administrateur',
        'agent': 'Agent fiscal',
        'superviseur': 'Superviseur'
      }
      return roles[role] || role
    }

    onMounted(() => {
      loadData()
    })

    return {
      user,
      activeTab,
      tabs,
      litigeFilter,
      searchDeclaration,
      searchContribuable,
      litiges,
      declarations,
      contribuables,
      stats,
      statsDetails,
      recentActivities,
      litigesEnAttente,
      litigesEnCours,
      litigesResolus,
      litigesFiltres,
      declarationsFiltrees,
      contribuablesFiltres,
      assignerLitige,
      voirDetailsLitige,
      voirDetailsDeclaration,
      voirProfilContribuable,
      handleLogout,
      formatMontant,
      formatDate,
      getStatutLabel,
      getRoleLabel
    }
  }
}
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background: #f5f7fa;
}

/* Header */
.dashboard-header {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  color: white;
  padding: 20px 40px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.header-content {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h1 {
  margin: 0;
  font-size: 1.8em;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 20px;
}

.user-details {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-name {
  font-weight: 600;
  font-size: 1.1em;
}

.user-role {
  font-size: 0.9em;
  opacity: 0.9;
}

.btn-logout {
  padding: 10px 20px;
  background: rgba(255,255,255,0.2);
  color: white;
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-logout:hover {
  background: rgba(255,255,255,0.3);
}

/* Navigation */
.quick-nav {
  background: white;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  padding: 0 40px;
  display: flex;
  gap: 10px;
  overflow-x: auto;
}

.nav-btn {
  padding: 15px 25px;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  color: #666;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.nav-btn:hover {
  color: #f5576c;
}

.nav-btn.active {
  color: #f5576c;
  border-bottom-color: #f5576c;
}

.nav-icon {
  font-size: 1.2em;
}

/* Main content */
.dashboard-main {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 20px;
}

/* Welcome section */
.welcome-section {
  margin-bottom: 30px;
}

.welcome-section h2 {
  color: #333;
  font-size: 2em;
  margin-bottom: 10px;
}

.welcome-section p {
  color: #666;
  font-size: 1.1em;
}

/* Stats section */
.stats-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 25px;
  display: flex;
  align-items: center;
  gap: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-icon {
  font-size: 3em;
}

.stat-content h3 {
  color: #333;
  font-size: 2em;
  margin: 0 0 5px 0;
}

.stat-content p {
  color: #666;
  margin: 0;
  font-size: 0.95em;
}

/* Actions section */
.actions-section {
  margin-bottom: 40px;
}

.actions-section h3 {
  color: #333;
  font-size: 1.5em;
  margin-bottom: 20px;
}

.action-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.action-card {
  background: white;
  border-radius: 12px;
  padding: 30px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  position: relative;
}

.action-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.action-icon {
  font-size: 3em;
  margin-bottom: 15px;
}

.action-card h4 {
  color: #333;
  margin: 10px 0;
  font-size: 1.2em;
}

.action-card p {
  color: #666;
  font-size: 0.9em;
  margin: 0;
}

.badge {
  position: absolute;
  top: 10px;
  right: 10px;
  background: #f5576c;
  color: white;
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 0.85em;
  font-weight: 600;
}

/* Activity section */
.activity-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.activity-section h3 {
  color: #333;
  margin-bottom: 20px;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.activity-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
}

.activity-icon {
  font-size: 2em;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
}

.activity-content {
  flex: 1;
}

.activity-text {
  margin: 0;
  color: #333;
  font-weight: 500;
}

.activity-time {
  color: #999;
  font-size: 0.85em;
}

/* Content section */
.content-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  flex-wrap: wrap;
  gap: 15px;
}

.section-header h2 {
  color: #333;
  margin: 0;
  font-size: 1.8em;
}

.filter-buttons {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 8px 16px;
  background: #f0f0f0;
  border: 2px solid transparent;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  color: #666;
  transition: all 0.3s ease;
}

.filter-btn:hover {
  background: #e0e0e0;
}

.filter-btn.active {
  background: #f5576c;
  color: white;
  border-color: #f5576c;
}

.search-input {
  padding: 10px 20px;
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  font-size: 1em;
  width: 300px;
  transition: border-color 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #f5576c;
}

/* Litiges grid */
.litiges-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.litige-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  border-left: 4px solid #f5576c;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.litige-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.litige-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  margin-bottom: 15px;
  gap: 10px;
}

.litige-header h4 {
  margin: 0;
  color: #333;
  font-size: 1.2em;
  flex: 1;
}

.litige-statut {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.85em;
  font-weight: 600;
  white-space: nowrap;
}

.litige-statut.en_attente {
  background: #fff3cd;
  color: #856404;
}

.litige-statut.en_cours {
  background: #cce5ff;
  color: #004085;
}

.litige-statut.resolu {
  background: #d4edda;
  color: #155724;
}

.litige-description {
  color: #666;
  margin-bottom: 15px;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.litige-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  font-size: 0.9em;
  color: #666;
  flex-wrap: wrap;
  gap: 10px;
}

.litige-contribuable,
.litige-date {
  display: flex;
  align-items: center;
  gap: 5px;
}

.litige-actions {
  display: flex;
  gap: 10px;
}

.btn-primary-small,
.btn-secondary-small {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9em;
  transition: all 0.3s ease;
}

.btn-primary-small {
  background: #f5576c;
  color: white;
}

.btn-primary-small:hover {
  background: #f093fb;
}

.btn-secondary-small {
  background: #e0e0e0;
  color: #333;
}

.btn-secondary-small:hover {
  background: #d0d0d0;
}

/* Table */
.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

.data-table thead {
  background: #f8f9fa;
}

.data-table th {
  padding: 15px;
  text-align: left;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #e0e0e0;
}

.data-table td {
  padding: 15px;
  border-bottom: 1px solid #e0e0e0;
  color: #666;
}

.data-table tbody tr:hover {
  background: #f8f9fa;
}

.contrib-info {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.contrib-info strong {
  color: #333;
}

.contrib-info small {
  color: #999;
  font-size: 0.85em;
}

.table-statut {
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.85em;
  font-weight: 600;
  display: inline-block;
}

.table-statut.brouillon {
  background: #fff3cd;
  color: #856404;
}

.table-statut.validee {
  background: #d4edda;
  color: #155724;
}

.btn-icon {
  background: #f0f0f0;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 1.2em;
  transition: all 0.3s ease;
}

.btn-icon:hover {
  background: #f5576c;
  transform: scale(1.1);
}

/* Stats grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.stat-box {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 25px;
  border-left: 4px solid #f5576c;
}

.stat-box h4 {
  color: #333;
  margin: 0 0 20px 0;
  font-size: 1.3em;
}

.stat-items {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.stat-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #e0e0e0;
}

.stat-row:last-child {
  border-bottom: none;
}

.stat-row span {
  color: #666;
}

.stat-row strong {
  color: #333;
  font-size: 1.1em;
}

/* Empty state */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
  font-size: 1.1em;
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard-header {
    padding: 15px 20px;
  }

  .header-content {
    flex-direction: column;
    gap: 20px;
  }

  .user-info {
    width: 100%;
    justify-content: space-between;
  }

  .quick-nav {
    padding: 0 20px;
  }

  .nav-btn {
    padding: 12px 15px;
    font-size: 0.9em;
  }

  .stats-section,
  .action-cards {
    grid-template-columns: 1fr;
  }

  .litiges-grid {
    grid-template-columns: 1fr;
  }

  .section-header {
    flex-direction: column;
    align-items: stretch;
  }

  .search-input {
    width: 100%;
  }

  .data-table {
    font-size: 0.9em;
  }

  .data-table th,
  .data-table td {
    padding: 10px;
  }
}
</style>